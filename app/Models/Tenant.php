<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'logo',
        'user_id',
        'plan_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the owner (user) of this tenant.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the plan for this tenant.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the users that belong to this tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if tenant is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate the tenant.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the tenant.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Check if tenant can add more users.
     */
    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->plan->max_users;
    }

    /**
     * Get the tenant's logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }

    /**
     * Scope to get only active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tenants by plan.
     */
    public function scopeByPlan($query, $planId)
    {
        return $query->where('plan_id', $planId);
    }

    /**
     * Scope to get tenants by owner.
     */
    public function scopeByOwner($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the modules that belong to this tenant.
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['is_active', 'custom_permissions', 'activated_at', 'expires_at'])
            ->withTimestamps();
    }

    /**
     * Get the active modules for this tenant.
     */
    public function activeModules()
    {
        return $this->modules()->wherePivot('is_active', true);
    }

    /**
     * Check if tenant has access to a specific module.
     */
    public function hasModule($moduleSlug): bool
    {
        return $this->activeModules()->where('slug', $moduleSlug)->exists();
    }

    /**
     * Assign a module to this tenant.
     */
    public function assignModule($moduleId, $customPermissions = null, $expiresAt = null): void
    {
        $this->modules()->attach($moduleId, [
            'is_active' => true,
            'custom_permissions' => $customPermissions,
            'activated_at' => now(),
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Remove a module from this tenant.
     */
    public function removeModule($moduleId): void
    {
        $this->modules()->detach($moduleId);
    }

    /**
     * Activate a module for this tenant.
     */
    public function activateModule($moduleId): void
    {
        $this->modules()->updateExistingPivot($moduleId, [
            'is_active' => true,
            'activated_at' => now(),
        ]);
    }

    /**
     * Deactivate a module for this tenant.
     */
    public function deactivateModule($moduleId): void
    {
        $this->modules()->updateExistingPivot($moduleId, [
            'is_active' => false,
        ]);
    }

    /**
     * Get modules based on plan.
     */
    public function getModulesByPlan()
    {
        // Get modules based on plan features
        $planFeatures = $this->plan->features ?? [];

        return Module::whereIn('slug', $planFeatures)->get();
    }
}
