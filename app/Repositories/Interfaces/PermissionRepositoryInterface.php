<?php

namespace App\Repositories\Interfaces;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PermissionRepositoryInterface
{
    /**
     * Get all permissions.
     */
    public function all(): Collection;

    /**
     * Find permission by ID.
     */
    public function find(int $id): ?Permission;

    /**
     * Find permission by ID or throw exception.
     */
    public function findOrFail(int $id): Permission;

    /**
     * Create a new permission.
     */
    public function create(array $data): Permission;

    /**
     * Update an existing permission.
     */
    public function update(int $id, array $data): Permission;

    /**
     * Delete a permission.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated permissions with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Get permissions by guard.
     */
    public function getPermissionsByGuard(string $guard): Collection;

    /**
     * Get permissions by module.
     */
    public function getPermissionsByModule(string $moduleSlug): Collection;

    /**
     * Search permissions by name.
     */
    public function searchPermissions(string $search): Collection;

    /**
     * Get permission statistics.
     */
    public function getPermissionStatistics(): array;
}
