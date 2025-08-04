<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Str;

class UserService
{
    protected $repo;

    public function __construct(UserRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Handles the business logic for creating a new user.
     * This includes hashing the password before saving.
     *
     * @param array $data The validated data from the request.
     * @return User
     */

    public function createUser(array $data): User
    {
        $tenant = Tenant::create([
            'id' => (string) Str::uuid(),
            'data' => [
                'company' => $data['company'],
            ]
        ]);

        $tenant->domains()->create([
            'domain' => $data['domain'],
        ]);

        $preparedData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tenant_id' => $tenant->id,
        ];

        $user = $this->repo->create($preparedData);

        // Auto-create profile for the new user
        Profile::create([
            'name' => $data['name'],
            'user_id' => $user->id,
        ]);

        return $user;
    }
}
