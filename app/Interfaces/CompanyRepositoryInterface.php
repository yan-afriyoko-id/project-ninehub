<?php

namespace App\Interfaces;
use App\Models\Company;

interface CompanyRepositoryInterface
{
    public function all(array $relations = []): iterable;
    public function create(array $data): Company;
    public function getById($id, array $relations = []): ?Company;
    public function update(Company $Company, array $data): Company;
    public function delete($id): bool;
    public function findByUserId(int $userId): ?Company;

}
