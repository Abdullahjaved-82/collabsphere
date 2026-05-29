<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;

class ProjectService
{
    public function createProject(array $data, Team $team, User $creator): Project
    {
        $data['team_id'] = $team->id;
        $data['created_by'] = $creator->id;
        $data['status'] = $data['status'] ?? 'planning';

        $project = Project::create($data);

        // Notify other team members
        $members = $team->users()->get();
        foreach ($members as $member) {
            if ($member->id !== $creator->id) {
                \App\Models\Notification::create([
                    'user_id' => $member->id,
                    'type' => 'project_created',
                    'data' => [
                        'message' => "New project '{$project->title}' created in '{$team->name}'",
                        'project_id' => $project->id,
                        'icon' => '📁',
                        'type' => 'success'
                    ]
                ]);
            }
        }

        return $project;
    }

    public function getProjectsForTeam(int $teamId): \Illuminate\Support\Collection
    {
        return Project::with('creator', 'team.users')
            ->where('team_id', $teamId)
            ->get()
            ->map(function (Project $project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->description,
                    'status' => $project->status,
                    'deadline' => $project->deadline,
                    'team' => $project->team,
                    'creator' => $project->creator,
                    'task_count' => $project->tasks()->count(),
                    'task_stats' => [
                        'todo' => $project->tasks()->where('status', 'todo')->count(),
                        'in_progress' => $project->tasks()->where('status', 'in_progress')->count(),
                        'done' => $project->tasks()->where('status', 'done')->count(),
                    ],
                    'progress' => $this->calculateProgress($project),
                    'members' => $project->team->users->take(3),
                    'more_members' => max(0, $project->team->users()->count() - 3),
                ];
            });
    }

    public function calculateProgress(Project $project): int
    {
        $totalTasks = $project->tasks()->count();

        if ($totalTasks === 0) {
            return 0;
        }

        $doneTasks = $project->tasks()->where('status', 'done')->count();

        return (int) round(($doneTasks / $totalTasks) * 100);
    }
}
