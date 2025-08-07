<?php

namespace App\Interfaces;
use app\Models\Tenant;
interface TenantRepositoryInterface
{
    public function getData(): array;
    public function updateData(array $data): void;
}
