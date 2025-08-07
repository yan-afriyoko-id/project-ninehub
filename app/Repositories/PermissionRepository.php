<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionRepository implements PermissionRepositoryInterface
{
    protected Permission $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    /**
     * Get all permissions
     */
    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Find permission by ID
     */
    public function find(int $id): ?Permission
    {
        return $this->model->find($id);
    }

    /**
     * Find permission by ID or throw exception
     */
    public function findOrFail(int $id): Permission
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create new permission
     */
    public function create(array $data): Permission
    {
        return $this->model->create($data);
    }

    /**
     * Update existing permission
     */
    public function update(int $id, array $data): Permission
    {
        $permission = $this->findOrFail($id);
        $permission->update($data);
        return $permission->fresh();
    }

    /**
     * Delete permission
     */
    public function delete(int $id): bool
    {
        $permission = $this->findOrFail($id);
        return $permission->delete();
    }

    /**
     * Get paginated permissions with filters
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->orderBy('name');

        // Filter by guard
        if (isset($filters['guard_name'])) {
            $query->where('guard_name', $filters['guard_name']);
        }

        // Search by name
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get permissions by guard
     */
    public function getPermissionsByGuard(string $guard): Collection
    {
        return $this->model->where('guard_name', $guard)->orderBy('name')->get();
    }

    /**
     * Get permissions by module
     */
    public function getPermissionsByModule(string $moduleSlug): Collection
    {
        return $this->model->where('name', 'like', "{$moduleSlug}.%")->orderBy('name')->get();
    }

    /**
     * Search permissions by name
     */
    public function searchPermissions(string $search): Collection
    {
        return $this->model->where('name', 'like', "%{$search}%")->orderBy('name')->get();
    }

    /**
     * Get permission statistics
     */
    public function getPermissionStatistics(): array
    {
        $totalPermissions = $this->model->count();
        $byGuard = $this->model->selectRaw('guard_name, count(*) as count')
            ->groupBy('guard_name')
            ->pluck('count', 'guard_name')
            ->toArray();

        $byModule = [];
        $permissions = $this->model->get();

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            if (count($parts) > 1) {
                $module = $parts[0];
                if (!isset($byModule[$module])) {
                    $byModule[$module] = 0;
                }
                $byModule[$module]++;
            }
        }

        return [
            'total_permissions' => $totalPermissions,
            'by_guard' => $byGuard,
            'by_module' => $byModule,
        ];
    }
}
