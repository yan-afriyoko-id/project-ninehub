<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Services\Interfaces\RoleServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class RoleService implements RoleServiceInterface
{
    protected RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllRoles(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->repository->find($id);
    }

    public function createRole(array $data): Role
    {
        try {
            $role = $this->repository->create($data);
            return $role;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateRole(int $id, array $data): Role
    {
        try {
            $role = $this->repository->update($id, $data);
            return $role;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteRole(int $id): bool
    {
        try {
            $result = $this->repository->delete($id);
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getRolesByGuard(string $guard): Collection
    {
        return $this->repository->getRolesByGuard($guard);
    }

    public function searchRoles(string $search): Collection
    {
        return $this->repository->searchRoles($search);
    }

    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool
    {
        try {
            $role = $this->repository->findOrFail($roleId);
            $permissions = Permission::whereIn('id', $permissionIds)->get();

            $role->givePermissionTo($permissions);

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function removePermissionsFromRole(int $roleId, array $permissionIds): bool
    {
        try {
            $role = $this->repository->findOrFail($roleId);
            $permissions = Permission::whereIn('id', $permissionIds)->get();

            $role->revokePermissionTo($permissions);

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getRoleStatistics(): array
    {
        return $this->repository->getRoleStatistics();
    }
}
