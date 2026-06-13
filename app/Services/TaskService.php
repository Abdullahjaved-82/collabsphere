<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Database\DatabaseManager;

class TaskService
{
    public function __construct(protected DatabaseManager $db) {}

    /**
     * Create a new task with auto-positioning in its status column
     */
    public function createTask(array $data, Project $project): Task
    {
        $data['project_id'] = $project->id;
        $data['created_by'] = auth()->id();
        
        // Get the next position in this status column
        $maxPosition = Task::where('project_id', $project->id)
            ->where('status', $data['status'] ?? 'todo')
            ->max('position') ?? -1;
        
        $data['position'] = $maxPosition + 1;

        return Task::create($data);
    }

    /**
     * Move task to new column and position atomically
     */
    public function moveTask(Task $task, string $newStatus, int $newPosition): void
    {
        $oldStatus = $task->status;
        $oldPosition = $task->position;

        $this->db->transaction(function () use ($task, $newStatus, $newPosition, $oldStatus, $oldPosition) {
            // Reorder positions
            if ($oldStatus !== $newStatus) {
                // Moving to different column: make room at new position
                Task::where('project_id', $task->project_id)
                    ->where('status', $newStatus)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');

                // Close gap in old column
                Task::where('project_id', $task->project_id)
                    ->where('status', $oldStatus)
                    ->where('position', '>', $oldPosition)
                    ->decrement('position');
            } else {
                if ($newPosition < $oldPosition) {
                    Task::where('project_id', $task->project_id)
                        ->where('status', $oldStatus)
                        ->whereBetween('position', [$newPosition, $oldPosition - 1])
                        ->increment('position');
                } elseif ($newPosition > $oldPosition) {
                    Task::where('project_id', $task->project_id)
                        ->where('status', $oldStatus)
                        ->whereBetween('position', [$oldPosition + 1, $newPosition])
                        ->decrement('position');
                }
            }

            // Update task without firing observer events inside the transaction
            Task::withoutEvents(function () use ($task, $newStatus, $newPosition) {
                $task->update(['status' => $newStatus, 'position' => $newPosition]);
            });
        });

        // Fire observer logic AFTER transaction commits successfully
        if ($oldStatus !== $newStatus) {
            $task->refresh();
            $user = \Illuminate\Support\Facades\Auth::user();
            $userName = $user ? $user->name : 'System';

            $statusLabels = [
                'todo' => 'To Do',
                'in_progress' => 'In Progress',
                'review' => 'Review',
                'done' => 'Done',
            ];
            $newLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);

            try {
                \App\Models\Activity::create([
                    'user_id' => $user?->id,
                    'project_id' => $task->project_id,
                    'task_id' => $task->id,
                    'action' => 'moved',
                    'description' => "{$userName} moved '{$task->title}' to {$newLabel}",
                ]);

                if ($task->assigned_to && $user && $task->assigned_to !== $user->id) {
                    \App\Models\Notification::create([
                        'user_id' => $task->assigned_to,
                        'type' => 'task_status_updated',
                        'data' => [
                            'message' => "'{$task->title}' status updated to {$newLabel}",
                            'project_id' => $task->project_id,
                            'task_id' => $task->id,
                            'icon' => '⏱',
                            'type' => 'success'
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to log activity/notification for task move', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get all tasks for a project grouped by status
     */
    public function getKanbanData(Project $project): Collection
    {
        $statuses = ['todo', 'in_progress', 'review', 'done'];
        
        return collect($statuses)->map(function ($status) use ($project) {
            $tasks = $project->tasks()
                ->where('status', $status)
                ->with(['assignedUser', 'creator'])
                ->orderBy('position')
                ->get();
            
            return [
                'status' => $status,
                'label' => match($status) {
                    'todo' => 'To Do',
                    'in_progress' => 'In Progress',
                    'review' => 'Review',
                    'done' => 'Done',
                },
                'tasks' => $tasks,
            ];
        });
    }

    /**
     * Bulk reorder positions in a column
     */
    public function reorderColumn(array $taskIds, string $status): void
    {
        foreach ($taskIds as $index => $taskId) {
            Task::where('id', $taskId)
                ->where('status', $status)
                ->update(['position' => $index]);
        }
    }
}
