<?php

namespace App\Repositories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TenantRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Tenant;
    public function findOrFail(int $id): Tenant;
    public function create(array $data): Tenant;
    public function update(int $id, array $data): Tenant;
    public function delete(int $id): bool;
    public function paginate(array $filters = []): LengthAwarePaginator;
    public function getActiveTenants(): Collection;
    public function getInactiveTenants(): Collection;
    public function getTenantsByPlan(int $planId): Collection;
    public function searchTenants(string $search): Collection;
    public function getTenantStatistics(): array;
}

class TenantRepository implements TenantRepositoryInterface
{
    protected Tenant $model;

    public function __construct(Tenant $model)
    {
        $this->model = $model;
    }

    /**
     * Get all tenants
     */
    public function all(): Collection
    {
        return $this->model->with(['owner', 'plan'])->get();
    }

    /**
     * Find tenant by ID
     */
    public function find(int $id): ?Tenant
    {
        return $this->model->with(['owner', 'plan', 'users'])->find($id);
    }

    /**
     * Find tenant by ID or throw exception
     */
    public function findOrFail(int $id): Tenant
    {
        return $this->model->with(['owner', 'plan', 'users'])->findOrFail($id);
    }

    /**
     * Create new tenant
     */
    public function create(array $data): Tenant
    {
        return $this->model->create($data);
    }

    /**
     * Update existing tenant
     */
    public function update(int $id, array $data): Tenant
    {
        $tenant = $this->findOrFail($id);
        $tenant->update($data);
        return $tenant->fresh(['owner', 'plan']);
    }

    /**
     * Delete tenant
     */
    public function delete(int $id): bool
    {
        $tenant = $this->findOrFail($id);
        return $tenant->delete();
    }

    /**
     * Get paginated tenants with filters
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['owner', 'plan']);

        // Filter by status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by plan
        if (isset($filters['plan_id'])) {
            $query->where('plan_id', $filters['plan_id']);
        }

        // Search by name or email
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get active tenants
     */
    public function getActiveTenants(): Collection
    {
        return $this->model->active()->with(['owner', 'plan'])->get();
    }

    /**
     * Get inactive tenants
     */
    public function getInactiveTenants(): Collection
    {
        return $this->model->where('is_active', false)->with(['owner', 'plan'])->get();
    }

    /**
     * Get tenants by plan
     */
    public function getTenantsByPlan(int $planId): Collection
    {
        return $this->model->byPlan($planId)->with(['owner', 'plan'])->get();
    }

    /**
     * Search tenants
     */
    public function searchTenants(string $search): Collection
    {
        return $this->model->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })->with(['owner', 'plan'])->get();
    }

    /**
     * Get tenant statistics
     */
    public function getTenantStatistics(): array
    {
        return [
            'total_tenants' => $this->model->count(),
            'active_tenants' => $this->model->active()->count(),
            'inactive_tenants' => $this->model->where('is_active', false)->count(),
            'by_plan' => $this->model->with('plan')
                ->get()
                ->groupBy('plan.slug')
                ->map(function ($tenants) {
                    return $tenants->count();
                }),
        ];
    }
}
