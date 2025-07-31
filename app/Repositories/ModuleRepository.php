<?php

namespace App\Repositories;

use App\Models\Module;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected Module $model;

    public function __construct(Module $model)
    {
        $this->model = $model;
    }

    /**
     * Get all modules.
     */
    public function all(): Collection
    {
        return $this->model->with(['tenants'])->get();
    }

    /**
     * Find module by ID.
     */
    public function find(int $id): ?Module
    {
        return $this->model->with(['tenants'])->find($id);
    }

    /**
     * Find module by ID or throw exception.
     */
    public function findOrFail(int $id): Module
    {
        return $this->model->with(['tenants'])->findOrFail($id);
    }

    /**
     * Create a new module.
     */
    public function create(array $data): Module
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing module.
     */
    public function update(int $id, array $data): Module
    {
        $module = $this->findOrFail($id);
        $module->update($data);
        return $module->fresh();
    }

    /**
     * Delete a module.
     */
    public function delete(int $id): bool
    {
        $module = $this->findOrFail($id);
        return $module->delete();
    }

    /**
     * Get active modules.
     */
    public function getActiveModules(): Collection
    {
        return $this->model->active()->with(['tenants'])->get();
    }

    /**
     * Get public modules.
     */
    public function getPublicModules(): Collection
    {
        return $this->model->public()->with(['tenants'])->get();
    }

    /**
     * Get modules ordered by order field.
     */
    public function getModulesByOrder(): Collection
    {
        return $this->model->ordered()->with(['tenants'])->get();
    }

    /**
     * Search modules by name, slug, or description.
     */
    public function searchModules(string $search): Collection
    {
        return $this->model->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        })->with(['tenants'])->get();
    }

    /**
     * Get modules by slugs.
     */
    public function getModulesBySlug(array $slugs): Collection
    {
        return $this->model->whereIn('slug', $slugs)->with(['tenants'])->get();
    }
}
