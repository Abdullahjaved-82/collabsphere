<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\Interfaces\TeamRepositoryInterface;
use Illuminate\Support\Collection;

class TeamRepository implements TeamRepositoryInterface
{
    public function all(): Collection
    {
        return Team::all();
    }

    public function find(int $id): ?Team
    {
        return Team::find($id);
    }

    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $team = Team::find($id);
        if ($team) {
            return $team->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $team = Team::find($id);
        if ($team) {
            return $team->delete();
        }
        return false;
    }

    public function findByInviteCode(string $code): ?Team
    {
        return Team::where('invite_code', $code)->first();
    }

    public function getMembers(int $teamId): Collection
    {
        $team = Team::find($teamId);
        return $team ? $team->users : collect();
    }

    public function addMember(int $teamId, int $userId, string $role = 'member'): void
    {
        $team = Team::find($teamId);
        if ($team) {
            $team->users()->attach($userId, [
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }
}
