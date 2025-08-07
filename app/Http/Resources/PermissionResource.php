<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Additional computed fields
            'module' => $this->getModuleFromName(),
            'action' => $this->getActionFromName(),
            'roles_count' => $this->roles_count ?? $this->roles->count(),
        ];
    }

    /**
     * Get module name from permission name
     */
    private function getModuleFromName(): ?string
    {
        $parts = explode('.', $this->name);
        return count($parts) > 1 ? $parts[0] : null;
    }

    /**
     * Get action from permission name
     */
    private function getActionFromName(): ?string
    {
        $parts = explode('.', $this->name);
        return count($parts) > 1 ? $parts[1] : null;
    }
}
