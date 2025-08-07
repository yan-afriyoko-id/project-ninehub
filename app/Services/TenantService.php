<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Plan;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Services\Interfaces\TenantServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

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
        try {
            DB::beginTransaction();
            $tenant = $this->repository->create($data);

            // Assign modules based on plan
            $this->assignModulesToTenant($tenant);

            DB::commit();
            return $tenant->load(['owner', 'plan']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update existing tenant
     */
    public function updateTenant(int $id, array $data): Tenant
    {
        try {
            DB::beginTransaction();
            $tenant = $this->repository->update($id, $data);
            DB::commit();
            return $tenant;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete tenant
     */
    public function deleteTenant(int $id): bool
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
     * Activate tenant
     */
    public function activateTenant(int $id): bool
    {
        try {
            $tenant = $this->repository->findOrFail($id);
            $tenant->activate();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Suspend tenant
     */
    public function suspendTenant(int $id): bool
    {
        try {
            $tenant = $this->repository->findOrFail($id);
            $tenant->deactivate();
            return true;
        } catch (Exception $e) {
            return false;
        }
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
        $plan = $tenant->plan;
        if (!$plan || empty($plan->features)) {
            return;
        }

        $modules = \App\Models\Module::whereIn('slug', $plan->features)->get();
        foreach ($modules as $module) {
            $tenant->assignModule($module->id);
        }
    }
}
