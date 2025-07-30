<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string|null $industry
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $website
 * @property string|null $description
 * @property int $user_id
 */
class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'industry' => $this->industry,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'website' => $this->website,
            'description' => $this->description,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
