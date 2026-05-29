<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get aggregate statistics for a team.
     */
    public function getTeamStats(Team $team): array
    {
        $projectIds = $team->projects->pluck('id')->toArray();

        if (empty($projectIds)) {
            return [
                'total_tasks' => 0,
                'completed_percentage' => 0,
                'in_progress_count' => 0,
                'overdue_count' => 0,
                'avg_completion_time' => 0,
                'completed_trend' => 0,
                'created_trend' => 0,
            ];
        }

        $allTasks = Task::whereIn('project_id', $projectIds)->get();
        $totalCount = $allTasks->count();

        // 1. Completed Percentage
        $completedCount = $allTasks->where('status', 'done')->count();
        $completedPercentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        // 2. In Progress Count
        $inProgressCount = $allTasks->where('status', 'in_progress')->count();

        // 3. Overdue Count
        $overdueCount = Task::whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::today()->toDateString())
            ->count();

        // 4. Average Completion Time (in hours)
        $completedTasks = $allTasks->where('status', 'done');
        $totalHours = 0;
        foreach ($completedTasks as $task) {
            $totalHours += $task->created_at->diffInHours($task->updated_at);
        }
        $avgCompletionHours = $completedTasks->count() > 0 
            ? round($totalHours / $completedTasks->count(), 1) 
            : 0;

        // 5. Completion Trend (This week vs last week)
        $now = Carbon::now();
        $oneWeekAgo = Carbon::now()->subDays(7);
        $twoWeeksAgo = Carbon::now()->subDays(14);

        $completedThisWeek = Task::whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->whereBetween('updated_at', [$oneWeekAgo, $now])
            ->count();

        $completedLastWeek = Task::whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->whereBetween('updated_at', [$twoWeeksAgo, $oneWeekAgo])
            ->count();

        $completedTrend = $completedLastWeek > 0 
            ? round((($completedThisWeek - $completedLastWeek) / $completedLastWeek) * 100, 1) 
            : ($completedThisWeek > 0 ? 100 : 0);

        // 6. Created Tasks Trend
        $createdThisWeek = Task::whereIn('project_id', $projectIds)
            ->whereBetween('created_at', [$oneWeekAgo, $now])
            ->count();

        $createdLastWeek = Task::whereIn('project_id', $projectIds)
            ->whereBetween('created_at', [$twoWeeksAgo, $oneWeekAgo])
            ->count();

        $createdTrend = $createdLastWeek > 0 
            ? round((($createdThisWeek - $createdLastWeek) / $createdLastWeek) * 100, 1) 
            : ($createdThisWeek > 0 ? 100 : 0);

        return [
            'total_tasks' => $totalCount,
            'completed_percentage' => $completedPercentage,
            'in_progress_count' => $inProgressCount,
            'overdue_count' => $overdueCount,
            'avg_completion_time' => $avgCompletionHours,
            'completed_trend' => $completedTrend,
            'created_trend' => $createdTrend,
        ];
    }

    /**
     * Get project progress percentage list for bar chart.
     */
    public function getProjectProgress(Team $team): array
    {
        $projects = $team->projects()->with('tasks')->get();
        $progressData = [];

        foreach ($projects as $project) {
            $tasks = $project->tasks;
            $total = $tasks->count();
            $done = $tasks->where('status', 'done')->count();

            $progressData[] = [
                'name' => $project->title,
                'progress' => $total > 0 ? round(($done / $total) * 100) : 0,
                'total_tasks' => $total,
            ];
        }

        return $progressData;
    }

    /**
     * Get task completion velocity over the last X days.
     */
    public function getTaskVelocity(Team $team, int $days = 14): array
    {
        $projectIds = $team->projects->pluck('id')->toArray();
        
        // Initialize the array of dates with 0 value
        $velocity = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateString = Carbon::today()->subDays($i)->format('Y-m-d');
            $label = Carbon::today()->subDays($i)->format('M d');
            $velocity[$dateString] = [
                'label' => $label,
                'count' => 0,
            ];
        }

        if (empty($projectIds)) {
            return array_values($velocity);
        }

        // Query actual completions grouped by date
        $completions = Task::whereIn('project_id', $projectIds)
            ->where('status', 'done')
            ->where('updated_at', '>=', Carbon::today()->subDays($days))
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->get();

        foreach ($completions as $c) {
            // SQLite may return date formatted differently, normalize it
            $formattedDate = Carbon::parse($c->date)->format('Y-m-d');
            if (isset($velocity[$formattedDate])) {
                $velocity[$formattedDate]['count'] = (int) $c->count;
            }
        }

        return array_values($velocity);
    }

    /**
     * Get members' workload tasks per member for workload doughnut chart.
     */
    public function getMemberWorkload(Team $team): array
    {
        $projectIds = $team->projects->pluck('id')->toArray();
        $members = $team->users;
        $workload = [];

        foreach ($members as $member) {
            // Count active tasks assigned to this member in team projects
            $activeTaskCount = Task::whereIn('project_id', $projectIds)
                ->where('assigned_to', $member->id)
                ->where('status', '!=', 'done')
                ->count();

            $workload[] = [
                'name' => $member->name,
                'avatar' => $member->avatar ? asset('storage/' . $member->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($member->name) . '&size=32',
                'task_count' => $activeTaskCount,
            ];
        }

        return $workload;
    }
}
