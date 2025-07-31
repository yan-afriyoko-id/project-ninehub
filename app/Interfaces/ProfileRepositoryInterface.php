<?php

namespace App\Interfaces;
use App\Models\profile;

interface profileRepositoryInterface
{
    public function all(array $relations = []): iterable;
    public function create(array $data): profile;
    public function getById($id, array $relations = []): ?profile;
    public function update(profile $profile, array $data): profile;
    public function delete($id): bool;
    public function findByUserId(int $userId): ?Profile;

}
