<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Task;
    public function create(array $data): Task;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByProject(int $projectId): Collection;
    public function getAssignedTasks(int $userId): Collection;
}
