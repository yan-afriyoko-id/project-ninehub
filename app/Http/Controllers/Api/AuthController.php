<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
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

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        $user->load(['tenant.domains', 'roles', 'permissions']);

        $token = $user->createToken('auth-token')->plainTextToken;

        $user->token = $token;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $user->load(['tenant.domains', 'roles', 'permissions']);

        $user->token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => new UserResource($user),
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load(['roles', 'permissions']);

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully.',
            'data' => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }
}
