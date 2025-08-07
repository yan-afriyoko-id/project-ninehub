<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Services\SSOService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $userService;
    protected $ssoService;

    public function __construct(UserService $userService, SSOService $ssoService)
    {
        $this->userService = $userService;
        $this->ssoService = $ssoService;
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

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login SSO menggunakan token Sanctum yang sudah ada
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginSSO(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'email' => 'required|email',
            ]);

            $token = $request->input('token');
            $email = $request->input('email');

            // Verifikasi token Sanctum yang sudah ada
            $ssoUser = $this->ssoService->verifyToken($token, $email);

            if (!$ssoUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token or user not found.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Ambil user dari token Sanctum
            $user = $this->ssoService->getUserFromToken($token);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found from token.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Load relasi user
            $user->load(['tenant.domains', 'roles', 'permissions']);

            // Gunakan token yang sudah ada (tidak perlu buat token baru)
            $user->token = $token;

            return response()->json([
                'success' => true,
                'message' => 'SSO login successful.',
                'data' => new UserResource($user),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SSO login failed.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
