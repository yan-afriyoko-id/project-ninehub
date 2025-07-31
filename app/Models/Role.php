<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    use HasFactory;

    /**
     * The factory class for this model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\RoleFactory::new();
    }

    /**
     * Override the users relationship to fix the deletion issue.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            config('auth.providers.users.model'),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
    }
}
