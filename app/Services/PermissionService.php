<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Services\Interfaces\PermissionServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class PermissionService implements PermissionServiceInterface
{
    protected PermissionRepositoryInterface $repository;

    public function __construct(PermissionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all permissions with optional filters
     */
    public function getAllPermissions(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Get permission by ID
     */
    public function getPermissionById(int $id): ?Permission
    {
        return $this->repository->find($id);
    }

    /**
     * Create new permission
     */
    public function createPermission(array $data): Permission
    {
        try {
            DB::beginTransaction();
            $permission = $this->repository->create($data);
            DB::commit();
            return $permission;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update existing permission
     */
    public function updatePermission(int $id, array $data): Permission
    {
        try {
            DB::beginTransaction();
            $permission = $this->repository->update($id, $data);
            DB::commit();
            return $permission;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete permission
     */
    public function deletePermission(int $id): bool
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get permissions by guard
     */
    public function getPermissionsByGuard(string $guard): Collection
    {
        return $this->repository->getPermissionsByGuard($guard);
    }

    /**
     * Get permissions by module
     */
    public function getPermissionsByModule(string $moduleSlug): Collection
    {
        return $this->repository->getPermissionsByModule($moduleSlug);
    }

    /**
     * Search permissions by name
     */
    public function searchPermissions(string $search): Collection
    {
        return $this->repository->searchPermissions($search);
    }

    /**
     * Sync permissions from modules
     */
    public function syncPermissionsFromModules(): bool
    {
        try {
            DB::beginTransaction();

            $modules = \App\Models\Module::all();

            foreach ($modules as $module) {
                $permissions = $module->getPermissionsToCreate();

                foreach ($permissions as $permissionName) {
                    if (!Permission::where('name', $permissionName)->exists()) {
                        Permission::create([
                            'name' => $permissionName,
                            'guard_name' => 'web'
                        ]);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get permission statistics
     */
    public function getPermissionStatistics(): array
    {
        return $this->repository->getPermissionStatistics();
    }
}
