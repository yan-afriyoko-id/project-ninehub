<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo' => $this->logo,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            'owner' => [
                'id' => $this->owner->id ?? null,
                'name' => $this->owner->name ?? null,
                'email' => $this->owner->email ?? null,
            ],
            'plan' => [
                'id' => $this->plan->id ?? null,
                'name' => $this->plan->name ?? null,
                'slug' => $this->plan->slug ?? null,
                'price' => $this->plan->price ?? null,
            ],
            'users_count' => $this->users_count ?? $this->users->count(),
            'modules_count' => $this->modules_count ?? $this->modules->count(),
        ];
    }
}
