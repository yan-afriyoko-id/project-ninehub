<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Services\Interfaces\PlanServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    private PlanServiceInterface $planService;

    public function __construct(PlanServiceInterface $planService)
    {
        $this->planService = $planService;
    }

    /**
     * Display a listing of plans.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['is_active', 'search', 'price_min', 'price_max', 'per_page']);
            $plans = $this->planService->getAllPlans($filters);

            return response()->json([
                'success' => true,
                'data' => PlanResource::collection($plans),
                'pagination' => [
                    'current_page' => $plans->currentPage(),
                    'last_page' => $plans->lastPage(),
                    'per_page' => $plans->perPage(),
                    'total' => $plans->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve plans',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created plan.
     */
    public function store(StorePlanRequest $request): JsonResponse
    {
        try {
            $plan = $this->planService->createPlan($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully',
                'data' => new PlanResource($plan),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified plan.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $plan = $this->planService->getPlanById($id);

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PlanResource($plan),
                'message' => 'Plan retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified plan.
     */
    public function update(UpdatePlanRequest $request, int $id): JsonResponse
    {
        try {
            $plan = $this->planService->updatePlan($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Plan updated successfully',
                'data' => new PlanResource($plan),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->planService->deletePlan($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete plan',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active plans.
     */
    public function active(): JsonResponse
    {
        try {
            $plans = $this->planService->getActivePlans();

            return response()->json([
                'success' => true,
                'data' => PlanResource::collection($plans),
                'message' => 'Active plans retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active plans',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get free plans.
     */
    public function free(): JsonResponse
    {
        try {
            $plans = $this->planService->getFreePlans();

            return response()->json([
                'success' => true,
                'data' => PlanResource::collection($plans),
                'message' => 'Free plans retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve free plans',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get paid plans.
     */
    public function paid(): JsonResponse
    {
        try {
            $plans = $this->planService->getPaidPlans();

            return response()->json([
                'success' => true,
                'data' => PlanResource::collection($plans),
                'message' => 'Paid plans retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve paid plans',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search plans.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $plans = $this->planService->searchPlans($search);

            return response()->json([
                'success' => true,
                'data' => PlanResource::collection($plans),
                'message' => 'Plans retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search plans',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get plan statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->planService->getPlanStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Plan statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve plan statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
