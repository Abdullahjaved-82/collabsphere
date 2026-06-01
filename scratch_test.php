<?php
use App\Models\Task;

$task = Task::first();
if (!$task) {
    echo "No task found.\n";
    exit;
}

$project = $task->project;
if (!$project) {
    echo "No project found for task.\n";
    exit;
}

$isTeamLeader = $project->team->users()
    ->where('user_id', 1) // assuming user ID 1
    ->wherePivot('role', 'leader')
    ->exists();

echo "Task ID: {$task->id}\n";
echo "Team ID: {$project->team_id}\n";
echo "Is Team Leader (User 1): " . ($isTeamLeader ? 'Yes' : 'No') . "\n";
