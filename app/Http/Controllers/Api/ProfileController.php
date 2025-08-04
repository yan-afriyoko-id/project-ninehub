<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'gender', 'age_min', 'age_max', 'per_page']);
            $profiles = $this->service->getAllProfiles($filters);

            return response()->json([
                'success' => true,
                'data' => ProfileResource::collection($profiles),
                'pagination' => [
                    'current_page' => $profiles->currentPage(),
                    'last_page' => $profiles->lastPage(),
                    'per_page' => $profiles->perPage(),
                    'total' => $profiles->total(),
                ],
                'message' => 'Profiles retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profiles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $this->service->getProfileByUserId($user->id);

            if (!$profile) {
                // Auto-create profile if not exists
                $profile = $this->service->create([
                    'name' => $user->name,
                ], $user->id);
            }

            return response()->json([
                'success' => true,
                'data' => new ProfileResource($profile),
                'message' => 'Profile retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreProfileRequest $request)
    {
        try {
            $userId = Auth::id();
            $profile = $this->service->create($request->validated(), $userId);

            return response()->json([
                'success' => true,
                'message' => 'Profile created successfully',
                'data' => new ProfileResource($profile),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $profile = $this->service->getProfileById($id);

            return response()->json([
                'success' => true,
                'data' => new ProfileResource($profile),
                'message' => 'Profile retrieved successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(StoreProfileRequest $request, $id)
    {
        try {
            $updated = $this->service->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => new ProfileResource($updated),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Profile deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->service->getProfileStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Profile statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
