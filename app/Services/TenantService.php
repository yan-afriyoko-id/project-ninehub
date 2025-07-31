<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Plan;
use App\Repositories\TenantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

interface TenantServiceInterface
{
    public function getAllTenants(array $filters = []): LengthAwarePaginator;
    public function getTenantById(int $id): ?Tenant;
    public function createTenant(array $data): Tenant;
    public function updateTenant(int $id, array $data): Tenant;
    public function deleteTenant(int $id): bool;
    public function activateTenant(int $id): bool;
    public function suspendTenant(int $id): bool;
    public function getTenantStatistics(): array;
}

class TenantService implements TenantServiceInterface
{
    protected TenantRepositoryInterface $repository;

    public function __construct(TenantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all tenants with optional filters
     */
    public function getAllTenants(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Get tenant by ID
     */
    public function getTenantById(int $id): ?Tenant
    {
        return $this->repository->find($id);
    }

    /**
     * Create new tenant
     */
    public function createTenant(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $tenant = $this->repository->create($data);

            // Assign modules based on plan
            $this->assignModulesToTenant($tenant);

            return $tenant->load(['owner', 'plan']);
        });
    }

    /**
     * Update existing tenant
     */
    public function updateTenant(int $id, array $data): Tenant
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->repository->update($id, $data);
        });
    }

    /**
     * Delete tenant
     */
    public function deleteTenant(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }

    /**
     * Activate tenant
     */
    public function activateTenant(int $id): bool
    {
        $tenant = $this->repository->findOrFail($id);
        $tenant->activate();
        return true;
    }

    /**
     * Suspend tenant
     */
    public function suspendTenant(int $id): bool
    {
        $tenant = $this->repository->findOrFail($id);
        $tenant->deactivate();
        return true;
    }

    /**
     * Get tenant statistics
     */
    public function getTenantStatistics(): array
    {
        return $this->repository->getTenantStatistics();
    }

    /**
     * Assign modules to tenant based on plan
     */
    private function assignModulesToTenant(Tenant $tenant): void
    {
        $planFeatures = $tenant->plan->features ?? [];

        foreach ($planFeatures as $moduleSlug) {
            $module = \App\Models\Module::where('slug', $moduleSlug)->first();
            if ($module) {
                $tenant->assignModule($module->id);
            }
        }
    }
}
