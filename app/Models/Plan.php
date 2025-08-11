<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
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
        'price',
        'currency',
        'max_users',
        'max_storage',
        'features',
        'is_active',            
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenants that use this plan.
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Check if plan is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if plan is free.
     */
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ' . $this->currency;
    }

    /**
     * Scope to get only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get free plans.
     */
    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    /**
     * Scope to get paid plans.
     */
    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }
}
