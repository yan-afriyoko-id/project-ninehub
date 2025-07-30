<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $preparedData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        return $this->repo->create($preparedData);
    }
}
