<?php

namespace App\Services\Interfaces;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PlanServiceInterface
{
    /**
     * Get all plans with optional filters.
     */
    public function getAllPlans(array $filters = []): LengthAwarePaginator;

    /**
     * Get plan by ID.
     */
    public function getPlanById(int $id): ?Plan;

    /**
     * Create a new plan.
     */
    public function createPlan(array $data): Plan;

    /**
     * Update an existing plan.
     */
    public function updatePlan(int $id, array $data): Plan;

    /**
     * Delete a plan.
     */
    public function deletePlan(int $id): bool;

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
