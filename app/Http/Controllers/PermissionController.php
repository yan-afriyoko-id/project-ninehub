<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\Interfaces\PermissionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private PermissionServiceInterface $permissionService;

    public function __construct(PermissionServiceInterface $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of permissions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['guard_name', 'search', 'per_page']);
            $permissions = $this->permissionService->getAllPermissions($filters);

            return response()->json([
                'success' => true,
                'data' => PermissionResource::collection($permissions),
                'pagination' => [
                    'current_page' => $permissions->currentPage(),
                    'last_page' => $permissions->lastPage(),
                    'per_page' => $permissions->perPage(),
                    'total' => $permissions->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permissions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created permission.
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        try {
            $permission = $this->permissionService->createPermission($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => new PermissionResource($permission),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified permission.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $permission = $this->permissionService->getPermissionById($id);

            if (!$permission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PermissionResource($permission),
                'message' => 'Permission retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified permission.
     */
    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        try {
            $permission = $this->permissionService->updatePermission($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => new PermissionResource($permission),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->permissionService->deletePermission($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete permission',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get permissions by guard.
     */
    public function byGuard(string $guard): JsonResponse
    {
        try {
            $permissions = $this->permissionService->getPermissionsByGuard($guard);

            return response()->json([
                'success' => true,
                'data' => PermissionResource::collection($permissions),
                'message' => 'Permissions retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permissions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get permissions by module.
     */
    public function byModule(string $moduleSlug): JsonResponse
    {
        try {
            $permissions = $this->permissionService->getPermissionsByModule($moduleSlug);

            return response()->json([
                'success' => true,
                'data' => PermissionResource::collection($permissions),
                'message' => 'Permissions retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permissions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search permissions.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $permissions = $this->permissionService->searchPermissions($search);

            return response()->json([
                'success' => true,
                'data' => PermissionResource::collection($permissions),
                'message' => 'Permissions retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search permissions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync permissions from modules.
     */
    public function sync(): JsonResponse
    {
        try {
            $result = $this->permissionService->syncPermissionsFromModules();

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sync permissions',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permissions synced successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync permissions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get permission statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->permissionService->getPermissionStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Permission statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permission statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
