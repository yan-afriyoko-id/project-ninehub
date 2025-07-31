<?php

namespace App\Services\Interfaces;

use App\Models\Module;
use Illuminate\Database\Eloquent\Collection;

interface ModuleServiceInterface
{
    /**
     * Get all modules.
     */
    public function getAllModules(): Collection;

    /**
     * Get module by ID.
     */
    public function getModuleById(int $id): ?Module;

    /**
     * Create a new module.
     */
    public function createModule(array $data): Module;

    /**
     * Update an existing module.
     */
    public function updateModule(int $id, array $data): Module;

    /**
     * Delete a module.
     */
    public function deleteModule(int $id): bool;

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

    /**
     * Sync module permissions with Spatie Permission.
     */
    public function syncModulePermissions(int $moduleId): bool;
}
