<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTenantRequest;
use App\Http\Requests\Tenant\UpdateTenantRequest;
use App\Http\Resources\TenantResource;
use App\Services\Interfaces\TenantServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    private TenantServiceInterface $tenantService;

    public function __construct(TenantServiceInterface $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of tenants.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['is_active', 'plan_id', 'search', 'per_page']);
            $tenants = $this->tenantService->getAllTenants($filters);

            return response()->json([
                'success' => true,
                'data' => TenantResource::collection($tenants),
                'pagination' => [
                    'current_page' => $tenants->currentPage(),
                    'last_page' => $tenants->lastPage(),
                    'per_page' => $tenants->perPage(),
                    'total' => $tenants->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenants',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created tenant.
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        try {
            $tenant = $this->tenantService->createTenant($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => new TenantResource($tenant),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $tenant = $this->tenantService->getTenantById($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new TenantResource($tenant),
                'message' => 'Tenant retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified tenant.
     */
    public function update(UpdateTenantRequest $request, int $id): JsonResponse
    {
        try {
            $tenant = $this->tenantService->updateTenant($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully',
                'data' => new TenantResource($tenant),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->tenantService->deleteTenant($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete tenant',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tenant statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->tenantService->getTenantStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Tenant statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate tenant.
     */
    public function activate(int $id): JsonResponse
    {
        try {
            $result = $this->tenantService->activateTenant($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to activate tenant',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant activated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Suspend tenant.
     */
    public function suspend(int $id): JsonResponse
    {
        try {
            $result = $this->tenantService->suspendTenant($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to suspend tenant',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant suspended successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
