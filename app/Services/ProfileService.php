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


    public function getAllProfiles(array $filters = [])
    {
        return $this->repo->paginate($filters);
    }

    public function getProfileById(int $id): ?Profile
    {
        return $this->repo->find($id);
    }

    public function create(array $data, int $userId): Profile
    {
        $existingProfile = $this->repo->findByUserId($userId);

        if ($existingProfile) {
            return $this->repo->update($existingProfile->id, $data);
        }

        $preparedData = array_merge($data, ['user_id' => $userId]);
        return $this->repo->create($preparedData);
    }

    public function update(int $id, array $data): Profile
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function getProfileByUserId(int $userId): ?Profile
    {
        return $this->repo->findByUserId($userId);
    }

    public function getProfileStatistics(): array
    {
        return $this->repo->getProfileStatistics();
    }
}
