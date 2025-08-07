<?php

namespace App\Services\Interfaces;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PermissionServiceInterface
{
    /**
     * Get all permissions with optional filters.
     */
    public function getAllPermissions(array $filters = []): LengthAwarePaginator;

    /**
     * Get permission by ID.
     */
    public function getPermissionById(int $id): ?Permission;

    /**
     * Create a new permission.
     */
    public function createPermission(array $data): Permission;

    /**
     * Update an existing permission.
     */
    public function updatePermission(int $id, array $data): Permission;

    /**
     * Delete a permission.
     */
    public function deletePermission(int $id): bool;

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
     * Sync permissions from modules.
     */
    public function syncPermissionsFromModules(): bool;

    /**
     * Get permission statistics.
     */
    public function getPermissionStatistics(): array;
}
