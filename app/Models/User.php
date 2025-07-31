<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasModulePermissions;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasModulePermissions, HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
  
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the tenant that owns the user.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the tenants that this user owns.
     */
    public function ownedTenants()
    {
        return $this->hasMany(Tenant::class, 'user_id');
    }

    /**
     * Scope to get users by tenant.
     */
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Check if user belongs to a specific tenant.
     */
    public function belongsToTenant($tenantId): bool
    {
        return $this->tenant_id == $tenantId;
    }

    /**
     * Check if user owns a specific tenant.
     */
    public function ownsTenant($tenantId): bool
    {
        return $this->ownedTenants()->where('id', $tenantId)->exists();
    }
}
