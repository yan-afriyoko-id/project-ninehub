<?php

namespace App\Services;

use App\Models\Module;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Services\Interfaces\ModuleServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class ModuleService implements ModuleServiceInterface
{
    protected ModuleRepositoryInterface $repository;

    public function __construct(ModuleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all modules.
     */
    public function getAllModules(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get module by ID.
     */
    public function getModuleById(int $id): ?Module
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new module.
     */
    public function createModule(array $data): Module
    {
        try {
            DB::beginTransaction();

            $module = $this->repository->create($data);

            // Sync permissions if module has permissions
            if (!empty($data['permissions'])) {
                $this->syncModulePermissions($module->id);
            }

            DB::commit();
            return $module;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing module.
     */
    public function updateModule(int $id, array $data): Module
    {
        try {
            DB::beginTransaction();

            $module = $this->repository->update($id, $data);

            // Sync permissions if permissions were updated
            if (isset($data['permissions'])) {
                $this->syncModulePermissions($module->id);
            }

            DB::commit();
            return $module;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a module.
     */
    public function deleteModule(int $id): bool
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
     * Get active modules.
     */
    public function getActiveModules(): Collection
    {
        return $this->repository->getActiveModules();
    }

    /**
     * Get public modules.
     */
    public function getPublicModules(): Collection
    {
        return $this->repository->getPublicModules();
    }

    /**
     * Get modules ordered by order field.
     */
    public function getModulesByOrder(): Collection
    {
        return $this->repository->getModulesByOrder();
    }

    /**
     * Search modules by name, slug, or description.
     */
    public function searchModules(string $search): Collection
    {
        return $this->repository->searchModules($search);
    }

    /**
     * Get modules by slugs.
     */
    public function getModulesBySlug(array $slugs): Collection
    {
        return $this->repository->getModulesBySlug($slugs);
    }

    /**
     * Sync module permissions with Spatie Permission.
     */
    public function syncModulePermissions(int $moduleId): bool
    {
        try {
            $module = $this->repository->find($moduleId);

            if (!$module) {
                return false;
            }

            $permissions = $module->getPermissionsToCreate();

            foreach ($permissions as $permissionName) {
                // Create permission if it doesn't exist
                if (!\Spatie\Permission\Models\Permission::where('name', $permissionName)->exists()) {
                    \Spatie\Permission\Models\Permission::create([
                        'name' => $permissionName,
                        'guard_name' => 'web'
                    ]);
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
