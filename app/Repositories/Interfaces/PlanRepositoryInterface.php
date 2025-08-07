<?php

namespace App\Repositories\Interfaces;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PlanRepositoryInterface
{
    /**
     * Get all plans.
     */
    public function all(): Collection;

    /**
     * Find plan by ID.
     */
    public function find(int $id): ?Plan;

    /**
     * Find plan by ID or throw exception.
     */
    public function findOrFail(int $id): Plan;

    /**
     * Create a new plan.
     */
    public function create(array $data): Plan;

    /**
     * Update an existing plan.
     */
    public function update(int $id, array $data): Plan;

    /**
     * Delete a plan.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated plans with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Get active plans.
     */
    public function getActivePlans(): Collection;

    /**
     * Get free plans.
     */
    public function getFreePlans(): Collection;

    /**
     * Get paid plans.
     */
    public function getPaidPlans(): Collection;

    /**
     * Search plans by name or description.
     */
    public function searchPlans(string $search): Collection;

    /**
     * Get plan statistics.
     */
    public function getPlanStatistics(): array;
}
