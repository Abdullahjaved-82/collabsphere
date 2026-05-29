<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Activity;
use Illuminate\Support\Collection;

class ActivityService
{
    /**
     * Get recent activities for a team.
     */
    public function getRecentActivities(Team $team, int $limit = 10): Collection
    {
        $projectIds = $team->projects->pluck('id')->toArray();

        if (empty($projectIds)) {
            return collect([]);
        }

        return Activity::whereIn('project_id', $projectIds)
            ->with(['user', 'task'])
            ->latest('created_at')
            ->take($limit)
            ->get();
    }
}
