<?php

namespace App\Interfaces;

use App\Models\Profile;

interface ProfileRepositoryInterface
{
    public function all(array $relations = []): iterable;
    public function create(array $data): Profile;
    public function getById($id, array $relations = []): ?Profile;
    public function update(Profile $profile, array $data): Profile;
    public function delete($id): bool;
    public function findByUserId(int $userId): ?Profile;
}
