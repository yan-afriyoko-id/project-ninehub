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

    /**
     * Get the tenants that have access to this module.
     */
    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot(['is_active', 'custom_permissions', 'activated_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Get the active tenants for this module.
     */
    public function activeTenants()
    {
        return $this->tenants()->wherePivot('is_active', true);
    }

    /**
     * Check if module is assigned to a specific tenant.
     */
    public function isAssignedToTenant($tenantId): bool
    {
        return $this->activeTenants()->where('tenant_id', $tenantId)->exists();
    }

    /**
     * Get custom permissions for a specific tenant.
     */
    public function getCustomPermissionsForTenant($tenantId): array
    {
        $pivot = $this->tenants()->where('tenant_id', $tenantId)->first()->pivot ?? null;

        if (!$pivot) {
            return [];
        }

        return $pivot->custom_permissions ?? [];
    }

    /**
     * Get effective permissions for a tenant (combine default + custom).
     */
    public function getEffectivePermissionsForTenant($tenantId): array
    {
        $defaultPermissions = $this->getModulePermissions();
        $customPermissions = $this->getCustomPermissionsForTenant($tenantId);

        return array_merge($defaultPermissions, $customPermissions);
    }
}
