<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class SSOService
{
    /**
     * Verify SSO token menggunakan token Sanctum yang sudah ada
     *
     * @param string $token
     * @param string $email
     * @return array|null
     */
    public function verifyToken($token, $email)
    {
        try {
            // Cek apakah token Sanctum valid
            $personalAccessToken = PersonalAccessToken::findToken($token);
            
            if (!$personalAccessToken) {
                Log::error('Invalid Sanctum token provided for SSO');
                return null;
            }

            // Cek apakah token masih aktif
            if ($personalAccessToken->expires_at && $personalAccessToken->expires_at->isPast()) {
                Log::error('Sanctum token has expired');
                return null;
            }

            // Ambil user dari token
            $user = $personalAccessToken->tokenable;
            
            if (!$user) {
                Log::error('User not found from Sanctum token');
                return null;
            }

            // Cek apakah email sesuai
            if ($user->email !== $email) {
                Log::error('Email mismatch for SSO login');
                return null;
            }

            return [
                'email' => $user->email,
                'name' => $user->name,
                'user_id' => $user->id,
            ];
            
        } catch (\Exception $e) {
            Log::error('SSO verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user from Sanctum token
     *
     * @param string $token
     * @return User|null
     */
    public function getUserFromToken($token)
    {
        try {
            $personalAccessToken = PersonalAccessToken::findToken($token);
            
            if (!$personalAccessToken) {
                return null;
            }

            return $personalAccessToken->tokenable;
        } catch (\Exception $e) {
            Log::error('Failed to get user from token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate Sanctum token
     *
     * @param string $token
     * @return bool
     */
    public function validateSanctumToken($token)
    {
        try {
            $personalAccessToken = PersonalAccessToken::findToken($token);
            
            if (!$personalAccessToken) {
                return false;
            }

            // Cek apakah token masih aktif
            if ($personalAccessToken->expires_at && $personalAccessToken->expires_at->isPast()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Token validation failed: ' . $e->getMessage());
            return false;
        }
    }
} 