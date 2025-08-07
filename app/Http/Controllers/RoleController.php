<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\Interfaces\RoleServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private RoleServiceInterface $roleService;

    public function __construct(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['guard', 'search', 'per_page']);
            $roles = $this->roleService->getAllRoles($filters);

            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully',
                'data' => RoleResource::collection($roles),
                'pagination' => [
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                    'per_page' => $roles->perPage(),
                    'total' => $roles->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->roleService->createRole($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => new RoleResource($role)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role retrieved successfully',
                'data' => new RoleResource($role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->updateRole($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => new RoleResource($role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->roleService->deleteRole($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function byGuard(string $guard): JsonResponse
    {
        try {
            $roles = $this->roleService->getRolesByGuard($guard);

            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully',
                'data' => RoleResource::collection($roles)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $roles = $this->roleService->searchRoles($search);

            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully',
                'data' => RoleResource::collection($roles)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignPermissions(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'permission_ids' => 'required|array',
                'permission_ids.*' => 'integer|exists:permissions,id'
            ]);

            $success = $this->roleService->assignPermissionsToRole($id, $request->permission_ids);

            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned to role successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removePermissions(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'permission_ids' => 'required|array',
                'permission_ids.*' => 'integer|exists:permissions,id'
            ]);

            $success = $this->roleService->removePermissionsFromRole($id, $request->permission_ids);

            return response()->json([
                'success' => true,
                'message' => 'Permissions removed from role successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->roleService->getRoleStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Role statistics retrieved successfully',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve role statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
