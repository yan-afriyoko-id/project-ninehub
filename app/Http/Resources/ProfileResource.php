<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property int $age
 * @property string|null $gender
 * @property string|null $phone_number
 * @property string|null $address
 * @property string|null $birth_date
 * @property int $user_id
 */
class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $this->age,
            'gender' => $this->gender,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'user_id' => $this->user_id,
        ];
    }
}
