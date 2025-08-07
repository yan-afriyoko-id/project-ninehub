<?php

namespace App\Interfaces;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProfileRepositoryInterface
{
    /**
     * Get all profiles.
     */
    public function all(): Collection;

    /**
     * Find profile by ID.
     */
    public function find(int $id): ?Profile;

    /**
     * Find profile by ID or throw exception.
     */
    public function findOrFail(int $id): Profile;

    /**
     * Create a new profile.
     */
    public function create(array $data): Profile;

    /**
     * Update an existing profile.
     */
    public function update(int $id, array $data): Profile;

    /**
     * Delete a profile.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated profiles with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Find profile by user ID.
     */
    public function findByUserId(int $userId): ?Profile;

    /**
     * Get profile statistics.
     */
    public function getProfileStatistics(): array;
}
