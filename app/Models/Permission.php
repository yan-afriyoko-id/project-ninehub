<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * The factory class for this model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\PermissionFactory::new();
    }
}
