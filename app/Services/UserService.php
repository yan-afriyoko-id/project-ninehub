<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        // Auto-create profile for the new user (optional)
        try {
            Profile::create([
                'name' => $data['name'],
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail user creation
            Log::warning('Failed to create profile for user: ' . $user->id, [
                'error' => $e->getMessage()
            ]);
        }

        return $user;
    }

    /**
     * Find or create user by email for SSO login
     *
     * @param string $email
     * @param array $ssoUserData
     * @return User|null
     */
    public function findOrCreateUserByEmail(string $email, array $ssoUserData): ?User
    {
        try {
            // Cari user berdasarkan email
            $user = User::where('email', $email)->first();

            if ($user) {
                // Update user data jika diperlukan
                $user->update([
                    'name' => $ssoUserData['name'] ?? $user->name,
                ]);
                return $user;
            }

            // Jika user tidak ditemukan, buat user baru
            // Untuk SSO, kita perlu menentukan tenant_id
            // Anda bisa menyesuaikan logika ini sesuai kebutuhan
            $tenant = Tenant::first(); // Ambil tenant default atau sesuai logika bisnis

            if (!$tenant) {
                Log::error('No tenant found for SSO user creation');
                return null;
            }

            $user = User::create([
                'name' => $ssoUserData['name'] ?? 'SSO User',
                'email' => $email,
                'password' => Hash::make(Str::random(32)), // Password random untuk SSO user
                'tenant_id' => $tenant->id,
            ]);

            // Buat profile untuk user baru
            try {
                Profile::create([
                    'name' => $ssoUserData['name'] ?? 'SSO User',
                    'user_id' => $user->id,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create profile for SSO user: ' . $user->id, [
                    'error' => $e->getMessage()
                ]);
            }

            return $user;

        } catch (\Exception $e) {
            Log::error('Failed to find or create SSO user: ' . $e->getMessage());
            return null;
        }
    }
}
