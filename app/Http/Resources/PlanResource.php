<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'max_users' => $this->max_users,
            'max_storage' => $this->max_storage,
            'features' => $this->features,
            'is_active' => $this->is_active,
            'formatted_price' => $this->formatted_price,
            'is_free' => $this->isFree(),
            'tenants_count' => $this->whenLoaded('tenants', function () {
                return $this->tenants->count();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
