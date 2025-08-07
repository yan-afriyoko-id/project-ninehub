<?php

namespace App\Repositories\Interfaces;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    /**
     * Get all companies.
     */
    public function all(): Collection;

    /**
     * Find company by ID.
     */
    public function find(int $id): ?Company;

    /**
     * Find company by ID or throw exception.
     */
    public function findOrFail(int $id): Company;

    /**
     * Create a new company.
     */
    public function create(array $data): Company;

    /**
     * Update an existing company.
     */
    public function update(int $id, array $data): Company;

    /**
     * Delete a company.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated companies with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Get companies by user.
     */
    public function getCompaniesByUser(int $userId): Collection;

    /**
     * Search companies by name or email.
     */
    public function searchCompanies(string $search): Collection;

    /**
     * Get company statistics.
     */
    public function getCompanyStatistics(): array;
}
