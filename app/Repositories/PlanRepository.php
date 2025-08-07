<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Repositories\Interfaces\PlanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanRepository implements PlanRepositoryInterface
{
    protected Plan $model;

    public function __construct(Plan $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Plan
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Plan
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Plan
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Plan
    {
        $plan = $this->findOrFail($id);
        $plan->update($data);
        return $plan->fresh();
    }

    public function delete(int $id): bool
    {
        $plan = $this->findOrFail($id);
        return $plan->delete();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Apply filters
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('price', 'asc')->paginate($perPage);
    }

    public function getActivePlans(): Collection
    {
        return $this->model->active()->get();
    }

    public function getFreePlans(): Collection
    {
        return $this->model->free()->get();
    }

    public function getPaidPlans(): Collection
    {
        return $this->model->paid()->get();
    }

    public function searchPlans(string $search): Collection
    {
        return $this->model->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        })->get();
    }

    public function getPlanStatistics(): array
    {
        $totalPlans = $this->model->count();
        $activePlans = $this->model->active()->count();
        $freePlans = $this->model->free()->count();
        $paidPlans = $this->model->paid()->count();

        $priceRanges = [
            'free' => $this->model->free()->count(),
            'low' => $this->model->where('price', '>', 0)->where('price', '<=', 100000)->count(),
            'medium' => $this->model->where('price', '>', 100000)->where('price', '<=', 500000)->count(),
            'high' => $this->model->where('price', '>', 500000)->count(),
        ];

        return [
            'total_plans' => $totalPlans,
            'active_plans' => $activePlans,
            'free_plans' => $freePlans,
            'paid_plans' => $paidPlans,
            'price_ranges' => $priceRanges,
        ];
    }
}
