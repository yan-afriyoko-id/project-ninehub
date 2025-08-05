<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Services\Interfaces\CompanyServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private CompanyServiceInterface $companyService;

    public function __construct(CompanyServiceInterface $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display a listing of companies.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'user_id', 'per_page']);
            $companies = $this->companyService->getAllCompanies($filters);

            return response()->json([
                'success' => true,
                'data' => CompanyResource::collection($companies),
                'pagination' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve companies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created company.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            $company = $this->companyService->createCompany($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'data' => new CompanyResource($company),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified company.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $company = $this->companyService->getCompanyById($id);

            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company),
                'message' => 'Company retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified company.
     */
    public function update(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        try {
            $company = $this->companyService->updateCompany($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data' => new CompanyResource($company),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified company.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->companyService->deleteCompany($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete company',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get companies by user.
     */
    public function byUser(int $userId): JsonResponse
    {
        try {
            $companies = $this->companyService->getCompaniesByUser($userId);

            return response()->json([
                'success' => true,
                'data' => CompanyResource::collection($companies),
                'message' => 'Companies retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve companies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search companies.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $companies = $this->companyService->searchCompanies($search);

            return response()->json([
                'success' => true,
                'data' => CompanyResource::collection($companies),
                'message' => 'Companies retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search companies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get company statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->companyService->getCompanyStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Company statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve company statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
