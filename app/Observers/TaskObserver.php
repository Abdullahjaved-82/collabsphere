<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\Activity;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';

        Activity::create([
            'user_id' => $user?->id,
            'project_id' => $task->project_id,
            'task_id' => $task->id,
            'action' => 'created',
            'description' => "{$userName} created task '{$task->title}'",
        ]);

        // Send database notification if assigned to a teammate
        if ($task->assigned_to) {
            Notification::create([
                'user_id' => $task->assigned_to,
                'type' => 'task_assigned',
                'data' => [
                    'message' => "You have been assigned task '{$task->title}'",
                    'project_id' => $task->project_id,
                    'task_id' => $task->id,
                    'icon' => '📋',
                    'type' => 'info'
                ]
            ]);
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'System';

        // Log if status changed
        if ($task->wasChanged('status') || $task->status !== $task->getOriginal('status')) {
            $oldStatus = $task->getOriginal('status') ?? 'todo';
            $newStatus = $task->status;

            $statusLabels = [
                'todo' => 'To Do',
                'in_progress' => 'In Progress',
                'review' => 'Review',
                'done' => 'Done',
            ];

            $oldLabel = $statusLabels[$oldStatus] ?? ucfirst($oldStatus);
            $newLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);

            Activity::create([
                'user_id' => $user?->id,
                'project_id' => $task->project_id,
                'task_id' => $task->id,
                'action' => 'moved',
                'description' => "{$userName} moved '{$task->title}' to {$newLabel}",
            ]);

            // Notify assigned user if someone else completes or updates status
            if ($task->assigned_to && $user && $task->assigned_to !== $user->id) {
                Notification::create([
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
        }

        // Notify if assigned user changes
        if ($task->wasChanged('assigned_to') && $task->assigned_to) {
            Notification::create([
                'user_id' => $task->assigned_to,
                'type' => 'task_assigned',
                'data' => [
                    'message' => "You have been assigned task '{$task->title}'",
                    'project_id' => $task->project_id,
                    'task_id' => $task->id,
                    'icon' => '📋',
                    'type' => 'info'
                ]
            ]);
        }
    }
}
