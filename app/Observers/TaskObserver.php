<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\Activity;
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
        }
    }
}
