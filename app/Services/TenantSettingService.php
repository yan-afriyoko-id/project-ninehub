<?php

namespace App\Services;

use App\Interfaces\TenantRepositoryInterface;

class TenantSettingService
{
    protected TenantRepositoryInterface $repo;

    public function __construct(TenantRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getSettings(): array
    {
        return $this->repo->getData();
    }

    public function updateSettings(array $data): void
    {
        $this->repo->updateData($data);
    }
}
