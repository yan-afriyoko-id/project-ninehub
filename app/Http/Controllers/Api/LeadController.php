<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lead\StoreLeadRequest;
use App\Http\Requests\Lead\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Services\Interfaces\LeadServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    private LeadServiceInterface $leadService;

    public function __construct(LeadServiceInterface $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
     * Display a listing of leads.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'contact_id', 'status', 'per_page']);
            $leads = $this->leadService->getAllLeads($filters);

            return response()->json([
                'success' => true,
                'data' => LeadResource::collection($leads),
                'pagination' => [
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'per_page' => $leads->perPage(),
                    'total' => $leads->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leads',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created lead.
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        try {
            $lead = $this->leadService->createLead($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'data' => new LeadResource($lead),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified lead.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $lead = $this->leadService->getLeadById($id);

            if (!$lead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new LeadResource($lead),
                'message' => 'Lead retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve lead',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified lead.
     */
    public function update(UpdateLeadRequest $request, int $id): JsonResponse
    {
        try {
            $lead = $this->leadService->updateLead($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully',
                'data' => new LeadResource($lead),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->leadService->deleteLead($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete lead',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lead',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get leads by contact.
     */
    public function byContact(int $contactId): JsonResponse
    {
        try {
            $leads = $this->leadService->getLeadsByContact($contactId);

            return response()->json([
                'success' => true,
                'data' => LeadResource::collection($leads),
                'message' => 'Leads retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leads',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search leads.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $leads = $this->leadService->searchLeads($search);

            return response()->json([
                'success' => true,
                'data' => LeadResource::collection($leads),
                'message' => 'Leads retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search leads',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get lead statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->leadService->getLeadStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Lead statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve lead statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
