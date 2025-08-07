<?php

namespace App\Repositories;

use App\Interfaces\TenantRepositoryInterface;
use App\Models\Tenant;
use Log;

class TenantRepository implements TenantRepositoryInterface
{
    protected Tenant $tenant;

    public function __construct()
    {
        $this->tenant = tenant();
    }

    public function getData(): array
    {
        return $this->tenant->data ?? [];
    }

    public function updateData(array $data): void
    {
        $current = $this->tenant->data ?? [];
        $this->tenant->data = array_merge($current, $data);
        Log::info('Dirty', $this->tenant->getDirty()); // tambahkan ini
        $this->tenant->save();
        Log::info('DB data', ['data' => \DB::table('tenants')->where('id', $this->tenant->id)->value('data')]);


    }
}
