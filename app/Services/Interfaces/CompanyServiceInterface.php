<?php

namespace App\Services\Interfaces;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyServiceInterface
{
    /**
     * Get all companies with optional filters.
     */
    public function getAllCompanies(array $filters = []): LengthAwarePaginator;

    /**
     * Get company by ID.
     */
    public function getCompanyById(int $id): ?Company;

    /**
     * Create a new company.
     */
    public function createCompany(array $data): Company;

    /**
     * Update an existing company.
     */
    public function updateCompany(int $id, array $data): Company;

    /**
     * Delete a company.
     */
    public function deleteCompany(int $id): bool;

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
