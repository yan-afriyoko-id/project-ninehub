<?php

namespace App\Repositories\Interfaces;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TenantRepositoryInterface
{
    /**
     * Get all tenants.
     */
    public function all(): Collection;

    /**
     * Find tenant by ID.
     */
    public function find(int $id): ?Tenant;

    /**
     * Find tenant by ID or throw exception.
     */
    public function findOrFail(int $id): Tenant;

    /**
     * Create a new tenant.
     */
    public function create(array $data): Tenant;

    /**
     * Update an existing tenant.
     */
    public function update(int $id, array $data): Tenant;

    /**
     * Delete a tenant.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated tenants with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Get active tenants.
     */
    public function getActiveTenants(): Collection;

    /**
     * Get inactive tenants.
     */
    public function getInactiveTenants(): Collection;

    /**
     * Get tenants by plan.
     */
    public function getTenantsByPlan(int $planId): Collection;

    /**
     * Search tenants by name or email.
     */
    public function searchTenants(string $search): Collection;

    /**
     * Get tenant statistics.
     */
    public function getTenantStatistics(): array;
}
