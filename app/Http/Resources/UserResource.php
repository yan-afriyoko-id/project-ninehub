<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];

        // Add token if available
        if (isset($this->token)) {
            $data['token'] = $this->token;
        }

        // Add roles if loaded
        if ($this->relationLoaded('roles')) {
            $data['roles'] = $this->roles->pluck('name');
        }

        // Add permissions if loaded
        if ($this->relationLoaded('permissions')) {
            $data['permissions'] = $this->permissions->pluck('name');
        }

        // Add profile if loaded and exists
        if ($this->relationLoaded('profile') && $this->profile) {
            $data['profile'] = new ProfileResource($this->profile);
        }

        // Add tenant if loaded and exists
        if ($this->relationLoaded('tenant') && $this->tenant) {
            $data['tenant'] = [
                'id' => $this->tenant->id,
                'name' => $this->tenant->name,
                'domains' => $this->tenant->relationLoaded('domains')
                    ? $this->tenant->domains->pluck('domain')
                    : [],
            ];
        }

        return $data;
    }
}
