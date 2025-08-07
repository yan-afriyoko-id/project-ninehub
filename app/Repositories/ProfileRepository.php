<?php

namespace App\Repositories;

use App\Models\Profile;
use App\Interfaces\ProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function all(): Collection
    {
        return Profile::with('user')->get();
    }

    public function find(int $id): ?Profile
    {
        return Profile::with('user')->find($id);
    }

    public function findOrFail(int $id): Profile
    {
        return Profile::with('user')->findOrFail($id);
    }

    public function create(array $data): Profile
    {
        $profile = Profile::create($data);
        return $profile->load('user');
    }

    public function update(int $id, array $data): Profile
    {
        $profile = $this->findOrFail($id);
        $profile->update($data);
        return $profile->fresh('user');
    }

    public function delete(int $id): bool
    {
        $profile = $this->find($id);
        if ($profile) {
            return $profile->delete();
        }
        return false;
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Profile::with('user');

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['gender']) && !empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (isset($filters['age_min']) && !empty($filters['age_min'])) {
            $query->where('age', '>=', $filters['age_min']);
        }

        if (isset($filters['age_max']) && !empty($filters['age_max'])) {
            $query->where('age', '<=', $filters['age_max']);
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->paginate($perPage);
    }

    public function findByUserId(int $userId): ?Profile
    {
        return Profile::with('user')->where('user_id', $userId)->first();
    }

    public function getProfileStatistics(): array
    {
        return [
            'total_profiles' => Profile::count(),
            'profiles_by_gender' => [
                'male' => Profile::where('gender', 'male')->count(),
                'female' => Profile::where('gender', 'female')->count(),
                'not_specified' => Profile::whereNull('gender')->count(),
            ],
            'average_age' => Profile::whereNotNull('age')->avg('age'),
            'age_distribution' => [
                '18-25' => Profile::whereBetween('age', [18, 25])->count(),
                '26-35' => Profile::whereBetween('age', [26, 35])->count(),
                '36-45' => Profile::whereBetween('age', [36, 45])->count(),
                '46+' => Profile::where('age', '>=', 46)->count(),
            ],
        ];
    }
}
