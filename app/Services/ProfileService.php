<?php

namespace App\Services;

use App\Exceptions\profile\profileNotFoundException;
use App\Interfaces\profileRepositoryInterface;
use App\Models\profile;
use App\Services\UserService;
use App\Events\profileCreated;

class profileService
{
    protected $repo;
    protected $UserService;

    public function __construct(profileRepositoryInterface $repo, UserService $UserService)
    {
        $this->repo = $repo;
        $this->UserService = $UserService;
    }


    public function getAllprofiles()
    {
        return $this->repo->all(['user']);
    }


    public function create(array $data, int $userId): profile
    {
        $existingProfile = $this->repo->findByUserId($userId);

        if ($existingProfile) {
            $updatedProfile = $this->repo->update($existingProfile, $data);
            return $updatedProfile;
        }

        $preparedData = array_merge($data, ['user_id' => $userId]);
        $newProfile = $this->repo->create($preparedData);
        return $newProfile;
    }



    public function getprofileById($id): ?profile
    {
        $profile = $this->repo->getById($id, ['user']);
        return $profile;
    }

    public function update(profile $profile, array $data): profile
    {
        return $this->repo->update($profile, $data);
    }

    public function delete($id): bool
    {
        $deleted = $this->repo->delete($id);
        return true;
    }
}
