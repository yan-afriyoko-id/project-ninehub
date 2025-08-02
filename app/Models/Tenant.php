<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $casts = [
        'data' => 'array',
    ];


    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }
}
