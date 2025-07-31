<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'route',
        'order',
        'is_active',
        'is_public',
        'permissions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'permissions' => 'array',
        'order' => 'integer',
    ];

    /**
     * Get the permissions for this module.
     */
    public function getModulePermissions()
    {
        return $this->permissions ?? [];
    }

    /**
     * Check if module has specific permission.
     */
    public function hasPermission($permission): bool
    {
        return in_array($permission, $this->getModulePermissions());
    }

    /**
     * Get all permissions that should be created for this module.
     */
    public function getPermissionsToCreate(): array
    {
        $permissions = [];
        $moduleSlug = $this->slug;

        foreach ($this->getModulePermissions() as $permission) {
            $permissions[] = $moduleSlug . '.' . $permission;
        }

        return $permissions;
    }

    /**
     * Scope a query to only include active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include public modules.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to order modules by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
