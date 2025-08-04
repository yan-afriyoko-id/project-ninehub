<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $Profiles = $this->service->getAllProfiles();
        return ProfileResource::collection($Profiles);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $profile = $user->load('profile')->profile;

        if (!$profile) {
            // Auto-create profile if not exists
            Profile::create([
                'name' => $user->name,
                'user_id' => $user->id,
            ]);

            $user->load('profile');
            $profile = $user->profile;
        }

        return new ProfileResource($profile);
    }

    public function store(StoreProfileRequest $request)
    {
        $userId = Auth::id();
        $Profile = $this->service->create($request->validated(), $userId);
        return new ProfileResource($Profile);
    }

    public function show($id)
    {
        $Profile = $this->service->getProfileById($id);
        return new ProfileResource($Profile);
    }

    public function update(StoreProfileRequest $request, $id)
    {
        $Profile = $this->service->getProfileById($id);
        $updated = $this->service->update($Profile, $request->validated());
        return new ProfileResource($updated);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->noContent();
    }
}
