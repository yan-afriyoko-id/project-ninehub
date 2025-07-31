<?php

namespace App\Repositories\Interfaces;

use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;

interface ModuleRepositoryInterface
{
    /**
     * Get all modules.
     */
    public function all(): Collection;

    /**
     * Find module by ID.
     */
    public function find(int $id): ?Module;

    /**
     * Find module by ID or throw exception.
     */
    public function findOrFail(int $id): Module;

    /**
     * Create a new module.
     */
    public function create(array $data): Module;

    /**
     * Update an existing module.
     */
    public function update(int $id, array $data): Module;

    /**
     * Delete a module.
     */
    public function delete(int $id): bool;

    /**
     * Get active modules.
     */
    public function getActiveModules(): Collection;

    /**
     * Get public modules.
     */
    public function getPublicModules(): Collection;

    /**
     * Get modules ordered by order field.
     */
    public function getModulesByOrder(): Collection;

    /**
     * Search modules by name, slug, or description.
     */
    public function searchModules(string $search): Collection;

    /**
     * Get modules by slugs.
     */
    public function getModulesBySlug(array $slugs): Collection;
}
