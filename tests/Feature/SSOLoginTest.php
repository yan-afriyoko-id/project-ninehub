<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SSOLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat tenant default untuk testing
        Tenant::create([
            'id' => 'test-tenant-id',
            'data' => [
                'company' => 'Test Company',
            ]
        ]);
    }

    /** @test */
    public function it_can_login_with_valid_sso_token()
    {
        $response = $this->postJson('/api/login/sso', [
            'token' => 'valid-sso-token',
            'email' => 'test@example.com',
            'provider' => 'default'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'token',
                        'tenant'
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('SSO login successful.', $response->json('message'));
    }

    /** @test */
    public function it_returns_error_for_invalid_token()
    {
        $response = $this->postJson('/api/login/sso', [
            'token' => '',
            'email' => 'test@example.com',
            'provider' => 'default'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_returns_error_for_invalid_email()
    {
        $response = $this->postJson('/api/login/sso', [
            'token' => 'valid-token',
            'email' => 'invalid-email',
            'provider' => 'default'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_returns_error_for_invalid_provider()
    {
        $response = $this->postJson('/api/login/sso', [
            'token' => 'valid-token',
            'email' => 'test@example.com',
            'provider' => 'invalid-provider'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_creates_new_user_if_not_exists()
    {
        $email = 'newuser@example.com';

        $response = $this->postJson('/api/login/sso', [
            'token' => 'valid-sso-token',
            'email' => $email,
            'provider' => 'default'
        ]);

        $response->assertStatus(200);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }

    /** @test */
    public function it_updates_existing_user()
    {
        // Create existing user
        $user = User::create([
            'name' => 'Old Name',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'tenant_id' => 'test-tenant-id'
        ]);

        $response = $this->postJson('/api/login/sso', [
            'token' => 'valid-sso-token',
            'email' => 'existing@example.com',
            'provider' => 'default'
        ]);

        $response->assertStatus(200);

        // Verify user was updated
        $this->assertDatabaseHas('users', [
            'email' => 'existing@example.com',
            'name' => 'SSO User' // Updated name from SSO
        ]);
    }

    /** @test */
    public function it_works_with_different_providers()
    {
        $providers = ['google', 'microsoft', 'facebook', 'default'];

        foreach ($providers as $provider) {
            $response = $this->postJson('/api/login/sso', [
                'token' => 'valid-sso-token',
                'email' => "test-{$provider}@example.com",
                'provider' => $provider
            ]);

            $response->assertStatus(200);
        }
    }
} 