<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SettingRepository implements SettingRepositoryInterface
{
    protected Setting $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Setting
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Setting
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Setting
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Setting
    {
        $setting = $this->findOrFail($id);
        $setting->update($data);
        return $setting;
    }

    public function delete(int $id): bool
    {
        $setting = $this->findOrFail($id);
        return $setting->delete();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Apply filters
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('key', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('value', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        return $query->with('user')->paginate($filters['per_page'] ?? 15);
    }

    public function getSettingsByGroup(string $group): Collection
    {
        return $this->model->where('group', $group)->get();
    }

    public function findByKey(string $key): ?Setting
    {
        return $this->model->where('key', $key)->first();
    }

    public function getSettingsByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getSettingsByType(string $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    public function getPublicSettings(): Collection
    {
        return $this->model->where('is_public', true)->get();
    }

    public function getPrivateSettings(): Collection
    {
        return $this->model->where('is_public', false)->get();
    }

    public function searchSettings(string $search): Collection
    {
        return $this->model->where(function ($query) use ($search) {
            $query->where('key', 'like', '%' . $search . '%')
                ->orWhere('value', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        })->get();
    }

    public function getSettingStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'public' => $this->model->where('is_public', true)->count(),
            'private' => $this->model->where('is_public', false)->count(),
            'by_group' => $this->model->selectRaw('group, count(*) as count')
                ->groupBy('group')
                ->pluck('count', 'group')
                ->toArray(),
            'by_type' => $this->model->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }
}
