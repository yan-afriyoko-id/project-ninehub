<?php

namespace App\Services;

use App\Models\Plan;
use App\Repositories\Interfaces\PlanRepositoryInterface;
use App\Services\Interfaces\PlanServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class PlanService implements PlanServiceInterface
{
    protected PlanRepositoryInterface $repository;

    public function __construct(PlanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPlans(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function getPlanById(int $id): ?Plan
    {
        return $this->repository->find($id);
    }

    public function createPlan(array $data): Plan
    {
        try {
            $plan = $this->repository->create($data);
            return $plan;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updatePlan(int $id, array $data): Plan
    {
        try {
            $plan = $this->repository->update($id, $data);
            return $plan;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deletePlan(int $id): bool
    {
        try {
            $result = $this->repository->delete($id);
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getActivePlans(): Collection
    {
        return $this->repository->getActivePlans();
    }

    public function getFreePlans(): Collection
    {
        return $this->repository->getFreePlans();
    }

    public function getPaidPlans(): Collection
    {
        return $this->repository->getPaidPlans();
    }

    public function searchPlans(string $search): Collection
    {
        return $this->repository->searchPlans($search);
    }

    public function getPlanStatistics(): array
    {
        return $this->repository->getPlanStatistics();
    }
}
