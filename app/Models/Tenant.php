<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $casts = [
        'data' => 'array',
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
}
