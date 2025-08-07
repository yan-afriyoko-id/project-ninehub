<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{


    public function create(array $data): User
    {
        return User::create($data);
    }

    public function getByEmail(string $email)
    {
        return User::where('email', '=', $email)->first();
    }

}
