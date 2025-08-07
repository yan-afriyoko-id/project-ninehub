<?php

namespace App\Services\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleServiceInterface
{
    public function getAllRoles(array $filters = []): LengthAwarePaginator;
    public function getRoleById(int $id): ?Role;
    public function createRole(array $data): Role;
    public function updateRole(int $id, array $data): Role;
    public function deleteRole(int $id): bool;
    public function getRolesByGuard(string $guard): Collection;
    public function searchRoles(string $search): Collection;
    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool;
    public function removePermissionsFromRole(int $roleId, array $permissionIds): bool;
    public function getRoleStatistics(): array;
}
