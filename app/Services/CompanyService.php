<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Services\Interfaces\CompanyServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyService implements CompanyServiceInterface
{
    private CompanyRepositoryInterface $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all companies with optional filters.
     */
    public function getAllCompanies(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Get company by ID.
     */
    public function getCompanyById(int $id): ?Company
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new company.
     */
    public function createCompany(array $data): Company
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing company.
     */
    public function updateCompany(int $id, array $data): Company
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a company.
     */
    public function deleteCompany(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get companies by user.
     */
    public function getCompaniesByUser(int $userId): Collection
    {
        return $this->repository->getCompaniesByUser($userId);
    }

    /**
     * Search companies by name or email.
     */
    public function searchCompanies(string $search): Collection
    {
        return $this->repository->searchCompanies($search);
    }

    /**
     * Get company statistics.
     */
    public function getCompanyStatistics(): array
    {
        return $this->repository->getCompanyStatistics();
    }
}
