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
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

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

    /**
     * Redirect to Google OAuth
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirectToGoogle()
    {
        try {
            // For API, we need to create a state parameter manually
            $state = Str::random(40);
            
            $url = Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl();
            
            return response()->json([
                'success' => true,
                'message' => 'Google OAuth redirect URL',
                'data' => [
                    'redirect_url' => $url
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Google OAuth URL',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle Google OAuth callback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $googleUserData = $this->ssoService->handleGoogleUser($googleUser);
            $user = $this->ssoService->createOrUpdateUserFromGoogle($googleUserData);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create or update user from Google.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $user->load(['tenant.domains', 'roles', 'permissions']);
            $token = $user->createToken('google-auth-token')->plainTextToken;
            $user->token = $token;

            return response()->json([
                'success' => true,
                'message' => 'Google OAuth login successful.',
                'data' => new UserResource($user),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google OAuth login failed.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Set password for Google OAuth user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Check if user has password (Google OAuth users have null password)
            if ($user->password !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has password set.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $user->update([
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password set successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set password.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Complete profile for Google OAuth user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeProfile(Request $request)
    {
        try {
            $request->validate([
                'company' => 'required|string|max:255',
                'domain' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Check if user already has tenant (not Google OAuth user)
            if ($user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User profile already completed.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create tenant for Google OAuth user
            $tenant = \App\Models\Tenant::create([
                'id' => (string) Str::uuid(),
                'data' => [
                    'company' => $request->company,
                ]
            ]);

            $tenant->domains()->create([
                'domain' => $request->domain,
            ]);

            // Update user with tenant and password
            $user->update([
                'tenant_id' => $tenant->id,
                'password' => bcrypt($request->password),
            ]);

            // Load updated user data
            $user->load(['tenant.domains', 'roles', 'permissions']);

            return response()->json([
                'success' => true,
                'message' => 'Profile completed successfully.',
                'data' => new UserResource($user),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete profile.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
