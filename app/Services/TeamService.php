<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Exceptions\TeamFullException;
use Illuminate\Support\Str;

class TeamService
{
    private const MAX_MEMBERS = 8;

    public function createTeam(array $data, User $creator): Team
    {
        $data['created_by'] = $creator->id;
        $data['invite_code'] = strtoupper(Str::random(8));

        $team = Team::create($data);

        $team->users()->attach($creator->id, [
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        return $team;
    }

    public function joinTeam(string $inviteCode, User $user): Team
    {
        $team = Team::where('invite_code', $inviteCode)->firstOrFail();

        if ($team->users()->count() >= self::MAX_MEMBERS) {
            throw new TeamFullException();
        }

        if ($team->users()->where('user_id', $user->id)->exists()) {
            return $team;
        }

        $team->users()->attach($user->id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return $team;
    }

    public function getTeamWithMembers(int $teamId): ?Team
    {
        return Team::with('users')->find($teamId);
    }
}
