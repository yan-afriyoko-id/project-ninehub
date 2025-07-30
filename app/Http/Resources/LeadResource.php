<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $source
 * @property string $status
 * @property float|null $potential_value
 * @property string|null $notes
 */
class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'source' => $this->source,
            'status' => $this->status,
            'potential_value' => $this->potential_value,
            'notes' => $this->notes,
            'contact' => new ContactResource($this->whenLoaded('contact')),
        ];
    }
}
