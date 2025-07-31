<?php

namespace App\Repositories;

use App\Models\Company;
use App\Interfaces\CompanyRepositoryInterface;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function all(array $relations = []): iterable
    {
        return Company::with($relations)->get();
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function getById($id, array $relations = []): ?Company
    {
        return Company::with($relations)->find($id);
    }

    public function update(Company $Company, array $data): Company
    {
        $Company->update($data);
        return $Company;
    }
    public function delete($id): bool
    {
        $Company = Company::find($id);
        if ($Company) {
            return $Company->delete();
        }
        return false;
    }

    public function findByUserId(int $userId): ?Company
    {
        return Company::where('user_id', $userId)->first();
    }

}
