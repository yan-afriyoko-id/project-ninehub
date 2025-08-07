<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository implements RoleRepositoryInterface
{
    protected Role $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('permissions')->get();
    }

    public function find(int $id): ?Role
    {
        return $this->model->with('permissions')->find($id);
    }

    public function findOrFail(int $id): Role
    {
        return $this->model->with('permissions')->findOrFail($id);
    }

    public function create(array $data): Role
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->findOrFail($id);
        $role->update($data);
        return $role->fresh(['permissions']);
    }

    public function delete(int $id): bool
    {
        $role = $this->findOrFail($id);
        return $role->delete();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with('permissions');

        if (isset($filters['guard'])) {
            $query->where('guard_name', $filters['guard']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

    public function getRolesByGuard(string $guard): Collection
    {
        return $this->model->with('permissions')
            ->where('guard_name', $guard)
            ->get();
    }

    public function searchRoles(string $search): Collection
    {
        return $this->model->with('permissions')
            ->where('name', 'like', "%{$search}%")
            ->get();
    }

    public function getRoleStatistics(): array
    {
        $totalRoles = $this->model->count();
        $byGuard = $this->model->get()
            ->groupBy('guard_name')
            ->map(function ($roles) {
                return $roles->count();
            });

        return [
            'total_roles' => $totalRoles,
            'by_guard' => $byGuard,
        ];
    }
}
