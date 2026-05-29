<?php

namespace App\Repositories\Interfaces;

use App\Models\Team;
use Illuminate\Support\Collection;

interface TeamRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Team;
    public function create(array $data): Team;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByInviteCode(string $code): ?Team;
    public function getMembers(int $teamId): Collection;
    public function addMember(int $teamId, int $userId, string $role): void;
}
