<?php

namespace App\Repositories\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Role;
    public function findOrFail(int $id): Role;
    public function create(array $data): Role;
    public function update(int $id, array $data): Role;
    public function delete(int $id): bool;
    public function paginate(array $filters = []): LengthAwarePaginator;
    public function getRolesByGuard(string $guard): Collection;
    public function searchRoles(string $search): Collection;
    public function getRoleStatistics(): array;
}
