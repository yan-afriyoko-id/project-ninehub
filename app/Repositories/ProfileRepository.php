<?php

namespace App\Repositories;

use App\Models\profile;
use App\Interfaces\profileRepositoryInterface;

class profileRepository implements profileRepositoryInterface
{
    public function all(array $relations = []): iterable
    {
        return profile::with($relations)->get();
    }

    public function create(array $data): profile
    {
        return profile::create($data);
    }

    public function getById($id, array $relations = []): ?profile
    {
        return profile::with($relations)->find($id);
    }

    public function update(profile $profile, array $data): profile
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
