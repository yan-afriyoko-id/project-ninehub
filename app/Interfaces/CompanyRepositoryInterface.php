<?php

namespace App\Interfaces;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Company;
    public function findOrFail(int $id): Company;
    public function create(array $data): Company;
    public function update(int $id, array $data): Company;
    public function delete(int $id): bool;
    public function paginate(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator;
    public function getCompaniesByUser(int $userId): Collection;
    public function searchCompanies(string $search): Collection;
    public function getCompanyStatistics(): array;
}
