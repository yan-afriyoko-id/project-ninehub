<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\StoreSettingRequest;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private SettingServiceInterface $settingService;

    public function __construct(SettingServiceInterface $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display a listing of settings.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'group', 'type', 'is_public', 'user_id', 'per_page']);
            $settings = $this->settingService->getAllSettings($filters);

            return response()->json([
                'success' => true,
                'data' => SettingResource::collection($settings),
                'pagination' => [
                    'current_page' => $settings->currentPage(),
                    'last_page' => $settings->lastPage(),
                    'per_page' => $settings->perPage(),
                    'total' => $settings->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created setting.
     */
    public function store(StoreSettingRequest $request): JsonResponse
    {
        try {
            $setting = $this->settingService->createSetting($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully',
                'data' => new SettingResource($setting),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified setting.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $setting = $this->settingService->getSettingById($id);

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new SettingResource($setting),
                'message' => 'Setting retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified setting.
     */
    public function update(UpdateSettingRequest $request, int $id): JsonResponse
    {
        try {
            $setting = $this->settingService->updateSetting($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'data' => new SettingResource($setting),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified setting.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->settingService->deleteSetting($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete setting',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settings by group.
     */
    public function byGroup(string $group): JsonResponse
    {
        try {
            $settings = $this->settingService->getSettingsByGroup($group);

            return response()->json([
                'success' => true,
                'data' => SettingResource::collection($settings),
                'message' => 'Settings retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get setting by key.
     */
    public function byKey(string $key): JsonResponse
    {
        try {
            $setting = $this->settingService->getSettingByKey($key);

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new SettingResource($setting),
                'message' => 'Setting retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settings by user.
     */
    public function byUser(int $userId): JsonResponse
    {
        try {
            $settings = $this->settingService->getSettingsByUser($userId);

            return response()->json([
                'success' => true,
                'data' => SettingResource::collection($settings),
                'message' => 'Settings retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settings by type.
     */
    public function byType(string $type): JsonResponse
    {
        try {
            $settings = $this->settingService->getSettingsByType($type);

            return response()->json([
                'success' => true,
                'data' => SettingResource::collection($settings),
                'message' => 'Settings retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get public settings.
     */
    public function public(): JsonResponse
    {
        try {
            $settings = $this->settingService->getPublicSettings();

            return response()->json([
                'success' => true,
                'data' => SettingResource::collection($settings),
                'message' => 'Public settings retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve public settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get private settings.
     */
    public function private(): JsonResponse
    {
        try {
            $settings = $this->settingService->getPrivateSettings();

            return response()->json([
                'success' => true,
                'data' => SettingResource::collection($settings),
                'message' => 'Private settings retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve private settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search settings.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $settings = $this->settingService->searchSettings($search);

            return response()->json([
                'success' => true,
                'data' => SettingResource::collection($settings),
                'message' => 'Settings retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get setting statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->settingService->getSettingStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Setting statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve setting statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get setting value by key (simplified response).
     */
    public function getValue(string $key): JsonResponse
    {
        try {
            $setting = $this->settingService->getSettingByKey($key);

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->typed_value,
                    'type' => $setting->type,
                ],
                'message' => 'Setting value retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve setting value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set setting value by key.
     */
    public function setValue(Request $request, string $key): JsonResponse
    {
        try {
            $value = $request->input('value');
            $type = $request->input('type', 'string');
            $group = $request->input('group');
            $description = $request->input('description');
            $isPublic = $request->input('is_public', false);
            $userId = $request->input('user_id');

            $data = [
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
                'is_public' => $isPublic,
                'user_id' => $userId,
            ];

            $setting = $this->settingService->createOrUpdateSetting($key, $data);

            return response()->json([
                'success' => true,
                'message' => 'Setting value set successfully',
                'data' => new SettingResource($setting),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set setting value',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
