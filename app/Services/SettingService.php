<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Services\Interfaces\SettingServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SettingService implements SettingServiceInterface
{
    protected SettingRepositoryInterface $repository;

    public function __construct(SettingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllSettings(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function getSettingById(int $id): ?Setting
    {
        return $this->repository->find($id);
    }

    public function createSetting(array $data): Setting
    {
        return $this->repository->create($data);
    }

    public function updateSetting(int $id, array $data): Setting
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSetting(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getSettingsByGroup(string $group): Collection
    {
        return $this->repository->getSettingsByGroup($group);
    }

    public function getSettingByKey(string $key): ?Setting
    {
        return $this->repository->findByKey($key);
    }

    public function getSettingsByUser(int $userId): Collection
    {
        return $this->repository->getSettingsByUser($userId);
    }

    public function getSettingsByType(string $type): Collection
    {
        return $this->repository->getSettingsByType($type);
    }

    public function getPublicSettings(): Collection
    {
        return $this->repository->getPublicSettings();
    }

    public function getPrivateSettings(): Collection
    {
        return $this->repository->getPrivateSettings();
    }

    public function searchSettings(string $search): Collection
    {
        return $this->repository->searchSettings($search);
    }

    public function createOrUpdateSetting(string $key, array $data): Setting
    {
        $setting = $this->repository->findByKey($key);

        if ($setting) {
            return $this->repository->update($setting->id, $data);
        }

        return $this->repository->create($data);
    }

    public function getSettingStatistics(): array
    {
        return $this->repository->getSettingStatistics();
    }
}
