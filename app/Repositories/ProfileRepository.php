<?php

namespace App\Repositories;

use App\Models\Profile;
use App\Interfaces\ProfileRepositoryInterface;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function all(array $relations = []): iterable
    {
        return Profile::with($relations)->get();
    }

    public function create(array $data): Profile
    {
        return Profile::create($data);
    }

    public function getById($id, array $relations = []): ?Profile
    {
        return Profile::with($relations)->find($id);
    }

    public function update(Profile $profile, array $data): Profile
    {
        $profile->update($data);
        return $profile;
    }
    public function delete($id): bool
    {
        $profile = profile::find($id);
        if ($profile) {
            return $profile->delete();
        }
        return false;
    }

    public function findByUserId(int $userId): ?Profile
    {
        return Profile::where('user_id', $userId)->first();
    }
}
