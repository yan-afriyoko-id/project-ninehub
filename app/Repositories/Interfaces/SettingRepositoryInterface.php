<?php

namespace App\Repositories\Interfaces;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SettingRepositoryInterface
{
    /**
     * Get all settings.
     */
    public function all(): Collection;

    /**
     * Find setting by ID.
     */
    public function find(int $id): ?Setting;

    /**
     * Find setting by ID or throw exception.
     */
    public function findOrFail(int $id): Setting;

    /**
     * Create a new setting.
     */
    public function create(array $data): Setting;

    /**
     * Update an existing setting.
     */
    public function update(int $id, array $data): Setting;

    /**
     * Delete a setting.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated settings with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Get settings by group.
     */
    public function getSettingsByGroup(string $group): Collection;

    /**
     * Find setting by key.
     */
    public function findByKey(string $key): ?Setting;

    /**
     * Get settings by user.
     */
    public function getSettingsByUser(int $userId): Collection;

    /**
     * Get settings by type.
     */
    public function getSettingsByType(string $type): Collection;

    /**
     * Get public settings.
     */
    public function getPublicSettings(): Collection;

    /**
     * Get private settings.
     */
    public function getPrivateSettings(): Collection;

    /**
     * Search settings by key or value.
     */
    public function searchSettings(string $search): Collection;

    /**
     * Get setting statistics.
     */
    public function getSettingStatistics(): array;
}
