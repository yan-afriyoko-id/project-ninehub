<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTenantSettingRequest;
use App\Http\Resources\TenantSettingResource;
use App\Services\TenantSettingService;

class TenantSettingController extends Controller
{
    protected $service;

    public function __construct(TenantSettingService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        $settings = $this->service->getSettings();
        return new TenantSettingResource($settings);
    }

    public function update(UpdateTenantSettingRequest $request)
    {
        $this->service->updateSettings($request->validated());
        return response()->json(['message' => 'Settings updated']);
    }

}
