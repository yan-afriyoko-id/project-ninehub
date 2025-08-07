<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyRepository implements CompanyRepositoryInterface
{
    private Company $model;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }

    /**
     * Get all companies.
     */
    public function all(): Collection
    {
        return $this->model->with(['user', 'contacts'])->get();
    }

    /**
     * Find company by ID.
     */
    public function find(int $id): ?Company
    {
        return $this->model->with(['user', 'contacts'])->find($id);
    }

    /**
     * Find company by ID or throw exception.
     */
    public function findOrFail(int $id): Company
    {
        return $this->model->with(['user', 'contacts'])->findOrFail($id);
    }

    /**
     * Create a new company.
     */
    public function create(array $data): Company
    {
        $company = $this->model->create($data);
        return $company->load(['user', 'contacts']);
    }

    /**
     * Update an existing company.
     */
    public function update(int $id, array $data): Company
    {
        $company = $this->findOrFail($id);
        $company->update($data);
        return $company->fresh(['user', 'contacts']);
    }

    /**
     * Delete a company.
     */
    public function delete(int $id): bool
    {
        $company = $this->find($id);
        if ($company) {
            return $company->delete();
        }
        return false;
    }

    /**
     * Get paginated companies with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['user', 'contacts']);

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->paginate($perPage);
    }

    /**
     * Get companies by user.
     */
    public function getCompaniesByUser(int $userId): Collection
    {
        return $this->model->with(['user', 'contacts'])
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Search companies by name or email.
     */
    public function searchCompanies(string $search): Collection
    {
        return $this->model->with(['user', 'contacts'])
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            })
            ->get();
    }

    /**
     * Get company statistics.
     */
    public function getCompanyStatistics(): array
    {
        return [
            'total_companies' => $this->model->count(),
            'companies_with_contacts' => $this->model->has('contacts')->count(),
            'companies_without_contacts' => $this->model->doesntHave('contacts')->count(),
            'recent_companies' => $this->model->latest()->take(5)->count(),
            'companies_with_phone' => $this->model->whereNotNull('phone')->count(),
            'companies_without_phone' => $this->model->whereNull('phone')->count(),
        ];
    }
}
