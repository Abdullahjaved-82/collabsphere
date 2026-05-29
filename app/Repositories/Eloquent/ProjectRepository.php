<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function all(): Collection
    {
        return Project::all();
    }

    public function find(int $id): ?Project
    {
        return Project::find($id);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $project = Project::find($id);
        if ($project) {
            return $project->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $project = Project::find($id);
        if ($project) {
            return $project->delete();
        }
        return false;
    }

    public function getByTeam(int $teamId): Collection
    {
        return Project::where('team_id', $teamId)->get();
    }
}
