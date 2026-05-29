<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(): Collection
    {
        return Task::orderBy('position')->get();
    }

    public function find(int $id): ?Task
    {
        return Task::find($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $task = Task::find($id);
        if ($task) {
            return $task->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $task = Task::find($id);
        if ($task) {
            return $task->delete();
        }
        return false;
    }

    public function getByProject(int $projectId): Collection
    {
        return Task::where('project_id', $projectId)->orderBy('position')->get();
    }

    public function getAssignedTasks(int $userId): Collection
    {
        return Task::where('assigned_to', $userId)->orderBy('position')->get();
    }
}
