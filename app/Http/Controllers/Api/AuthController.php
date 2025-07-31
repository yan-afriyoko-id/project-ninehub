<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\auth\AuthResponse;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request): AuthResponse
    {
        $user = $this->userService->createUser($request->validated());
        $token = $user->createToken('auth-token')->plainTextToken;

        $data = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        return AuthResponse::success($data, 'Registration successful.', Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): AuthResponse
    {
        if (!Auth::attempt($request->validated())) {
            return AuthResponse::error('Invalid credentials.', Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        $data = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        return AuthResponse::success($data, 'Login successful.');
    }

    public function profile(Request $request): AuthResponse
    {
        $user = $request->user();
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
        return AuthResponse::success($data, 'User profile retrieved successfully.');
    }

    public function logout(Request $request): AuthResponse
    {
        $request->user()->tokens()->delete();

        return AuthResponse::success([], 'Logout successful.');
    }
}
