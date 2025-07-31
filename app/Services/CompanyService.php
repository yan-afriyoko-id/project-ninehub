<?php

namespace App\Services;

use App\Interfaces\CompanyRepositoryInterface;
use App\Models\Company;
use App\Services\UserService;
use App\Events\CompanyCreated;

class CompanyService
{
    protected $repo;
    protected $UserService;

    public function __construct(CompanyRepositoryInterface $repo, UserService $UserService)
    {
        $this->repo = $repo;
        $this->UserService = $UserService;
    }


    public function getAllCompanys()
    {
        return $this->repo->all(['user']);
    }


    public function create(array $data, int $userId): Company
    {
        $preparedData = array_merge($data, ['user_id' => $userId]);
        $Company = $this->repo->create($preparedData);
        return $Company;
    }

    public function getCompanyById($id): ?Company
    {
        $Company = $this->repo->getById($id, ['user']);
        return $Company;
    }

    public function update(Company $Company, array $data): Company
    {
        return $this->repo->update($Company, $data);
    }

    public function delete($id): bool
    {
        $deleted = $this->repo->delete($id);
        return true;
    }
}
