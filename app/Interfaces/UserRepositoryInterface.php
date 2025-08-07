<?php

namespace App\Interfaces;
use Illuminate\Http\Request;
use App\Models\User;

interface UserRepositoryInterface
{

    public function create(array $data): User;

    public function getByEmail(string $email);

}
