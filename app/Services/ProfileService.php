<?php

namespace App\Services;

use App\Interfaces\ProfileRepositoryInterface;
use App\Models\Profile;
use App\Services\UserService;

class ProfileService
{
    protected $repo;
    protected $UserService;

    public function __construct(ProfileRepositoryInterface $repo, UserService $UserService)
    {
        $this->repo = $repo;
        $this->UserService = $UserService;
    }


    public function getAllProfiles()
    {
        return $this->repo->all(['user']);
    }


    public function create(array $data, int $userId): Profile
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



    public function getProfileById($id): ?Profile
    {
        $profile = $this->repo->getById($id, ['user']);
        return $profile;
    }

    public function update(Profile $profile, array $data): Profile
    {
        return $this->repo->update($profile, $data);
    }

    public function delete($id): bool
    {
        $deleted = $this->repo->delete($id);
        return true;
    }
}
