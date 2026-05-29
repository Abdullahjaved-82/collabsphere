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
        $this->db->transaction(function () use ($task, $newStatus, $newPosition) {
            $oldStatus = $task->status;

            // If moving to different column, reorder all tasks in the new column
            if ($oldStatus !== $newStatus) {
                Task::where('project_id', $task->project_id)
                    ->where('status', $newStatus)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');
            } else {
                // Reordering within same column
                $oldPosition = $task->position;
                
                if ($newPosition < $oldPosition) {
                    // Moving up
                    Task::where('project_id', $task->project_id)
                        ->where('status', $oldStatus)
                        ->whereBetween('position', [$newPosition, $oldPosition - 1])
                        ->increment('position');
                } else {
                    // Moving down
                    Task::where('project_id', $task->project_id)
                        ->where('status', $oldStatus)
                        ->whereBetween('position', [$oldPosition + 1, $newPosition])
                        ->decrement('position');
                }
            }

            $task->update(['status' => $newStatus, 'position' => $newPosition]);
        });
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
