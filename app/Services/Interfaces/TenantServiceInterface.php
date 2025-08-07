<?php

namespace App\Services\Interfaces;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TenantServiceInterface
{
    /**
     * Get all tenants with optional filters.
     */
    public function getAllTenants(array $filters = []): LengthAwarePaginator;

    /**
     * Get tenant by ID.
     */
    public function getTenantById(int $id): ?Tenant;

    /**
     * Create a new tenant.
     */
    public function createTenant(array $data): Tenant;

    /**
     * Update an existing tenant.
     */
    public function updateTenant(int $id, array $data): Tenant;

    /**
     * Delete a tenant.
     */
    public function deleteTenant(int $id): bool;

    /**
     * Activate a tenant.
     */
    public function activateTenant(int $id): bool;

    /**
     * Suspend a tenant.
     */
    public function suspendTenant(int $id): bool;

    /**
     * Get tenant statistics.
     */
    public function getTenantStatistics(): array;
}
