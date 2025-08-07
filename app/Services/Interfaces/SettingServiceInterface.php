<?php

namespace App\Services\Interfaces;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SettingServiceInterface
{
    /**
     * Get all settings with optional filters.
     */
    public function getAllSettings(array $filters = []): LengthAwarePaginator;

    /**
     * Get setting by ID.
     */
    public function getSettingById(int $id): ?Setting;

    /**
     * Create a new setting.
     */
    public function createSetting(array $data): Setting;

    /**
     * Update an existing setting.
     */
    public function updateSetting(int $id, array $data): Setting;

    /**
     * Delete a setting.
     */
    public function deleteSetting(int $id): bool;

    /**
     * Get settings by group.
     */
    public function getSettingsByGroup(string $group): Collection;

    /**
     * Find setting by key.
     */
    public function getSettingByKey(string $key): ?Setting;

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
     * Create or update setting by key.
     */
    public function createOrUpdateSetting(string $key, array $data): Setting;

    /**
     * Get setting statistics.
     */
    public function getSettingStatistics(): array;
}
