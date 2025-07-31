<?php

namespace App\Services;

use App\Models\Module;
use App\Repositories\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

interface ModuleServiceInterface
{
    public function getAllModules(): Collection;
    public function getModuleById(int $id): ?Module;
    public function createModule(array $data): Module;
    public function updateModule(int $id, array $data): Module;
    public function deleteModule(int $id): bool;
    public function getActiveModules(): Collection;
    public function getPublicModules(): Collection;
    public function getModulesByOrder(): Collection;
    public function searchModules(string $search): Collection;
    public function getModulesBySlug(array $slugs): Collection;
    public function syncModulePermissions(int $moduleId): bool;
}

class ModuleService implements ModuleServiceInterface
{
    protected ModuleRepositoryInterface $repository;

    public function __construct(ModuleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all modules
     */
    public function getAllModules(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get module by ID
     */
    public function getModuleById(int $id): ?Module
    {
        return $this->repository->find($id);
    }

    /**
     * Create new module
     */
    public function createModule(array $data): Module
    {
        return DB::transaction(function () use ($data) {
            $module = $this->repository->create($data);

            // Sync permissions for this module
            $this->syncModulePermissions($module->id);

            return $module->load(['tenants']);
        });
    }

    /**
     * Update existing module
     */
    public function updateModule(int $id, array $data): Module
    {
        return DB::transaction(function () use ($id, $data) {
            $module = $this->repository->update($id, $data);

            // Sync permissions for this module
            $this->syncModulePermissions($module->id);

            return $module;
        });
    }

    /**
     * Delete module
     */
    public function deleteModule(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }

    /**
     * Get active modules
     */
    public function getActiveModules(): Collection
    {
        return $this->repository->getActiveModules();
    }

    /**
     * Get public modules
     */
    public function getPublicModules(): Collection
    {
        return $this->repository->getPublicModules();
    }

    /**
     * Get modules ordered by order field
     */
    public function getModulesByOrder(): Collection
    {
        return $this->repository->getModulesByOrder();
    }

    /**
     * Search modules
     */
    public function searchModules(string $search): Collection
    {
        return $this->repository->searchModules($search);
    }

    /**
     * Get modules by slugs
     */
    public function getModulesBySlug(array $slugs): Collection
    {
        return $this->repository->getModulesBySlug($slugs);
    }

    /**
     * Sync permissions for a module
     */
    public function syncModulePermissions(int $moduleId): bool
    {
        try {
            $module = $this->repository->findOrFail($moduleId);
            $permissions = $module->getPermissionsToCreate();

            foreach ($permissions as $permissionName) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
