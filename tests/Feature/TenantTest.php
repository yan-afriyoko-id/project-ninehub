<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase, WithFaker;



    /**
     * Test tenant creation.
     */
    public function test_can_create_tenant(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $tenantData = [
            'name' => 'Test Company',
            'email' => 'admin@testcompany.com',
            'phone' => '+62-21-1234567',
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'is_active' => true,
        ];

        $response = $this->withoutMiddleware()->postJson('/api/tenants', $tenantData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'owner',
                    'plan',
                    'users_count',
                    'modules_count',
                ]
            ]);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Company',
            'email' => 'admin@testcompany.com',
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
    }

    /**
     * Test tenant listing with filters.
     */
    public function test_can_list_tenants_with_filters(): void
    {
        // Clear database first
        Tenant::query()->delete();
        User::query()->delete();
        Plan::query()->delete();

        // Create test data
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Tenant::factory()->count(3)->active()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
        Tenant::factory()->count(2)->inactive()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->withoutMiddleware()->getJson('/api/tenants?is_active=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'is_active',
                        'created_at',
                        'updated_at',
                        'owner',
                        'plan',
                        'users_count',
                        'modules_count',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    /**
     * Test tenant details.
     */
    public function test_can_get_tenant_details(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->withoutMiddleware()->getJson("/api/tenants/{$tenant->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'owner',
                    'plan',
                    'users_count',
                    'modules_count',
                ]
            ]);
    }

    /**
     * Test tenant update.
     */
    public function test_can_update_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $newPlan = Plan::factory()->create();

        $updateData = [
            'name' => 'Updated Company',
            'plan_id' => $newPlan->id,
            'is_active' => false,
        ];

        $response = $this->withoutMiddleware()->putJson("/api/tenants/{$tenant->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tenant updated successfully',
            ]);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Updated Company',
            'plan_id' => $newPlan->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test tenant activation.
     */
    public function test_can_activate_tenant(): void
    {
        $tenant = Tenant::factory()->inactive()->create();

        $response = $this->withoutMiddleware()->patchJson("/api/tenants/{$tenant->id}/activate");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tenant activated successfully',
            ]);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test tenant suspension.
     */
    public function test_can_suspend_tenant(): void
    {
        $tenant = Tenant::factory()->active()->create();

        $response = $this->withoutMiddleware()->patchJson("/api/tenants/{$tenant->id}/suspend");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tenant suspended successfully',
            ]);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test tenant statistics.
     */
    public function test_can_get_tenant_statistics(): void
    {
        // Clear database first
        Tenant::query()->delete();
        User::query()->delete();
        Plan::query()->delete();

        // Create test data
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Tenant::factory()->count(3)->active()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
        Tenant::factory()->count(2)->inactive()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->withoutMiddleware()->getJson('/api/tenants/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_tenants',
                    'active_tenants',
                    'inactive_tenants',
                    'by_plan',
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(5, $data['total_tenants']);
        $this->assertEquals(3, $data['active_tenants']);
        $this->assertEquals(2, $data['inactive_tenants']);
    }

    /**
     * Test tenant model methods.
     */
    public function test_tenant_model_methods(): void
    {
        $tenant = Tenant::factory()->active()->create();

        // Test status methods
        $this->assertTrue($tenant->isActive());

        // Test activation/deactivation
        $tenant->deactivate();
        $this->assertFalse($tenant->isActive());

        $tenant->activate();
        $this->assertTrue($tenant->isActive());

        // Test logo URL
        $this->assertNull($tenant->logo_url);

        $tenant->update(['logo' => 'logos/company.png']);
        $this->assertStringContainsString('logos/company.png', $tenant->logo_url);
    }

    /**
     * Test tenant scopes.
     */
    public function test_tenant_scopes(): void
    {
        // Clear database first
        Tenant::query()->delete();
        User::query()->delete();
        Plan::query()->delete();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Tenant::factory()->count(3)->active()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
        Tenant::factory()->count(2)->inactive()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertEquals(3, Tenant::active()->count());
        $this->assertEquals(2, Tenant::where('is_active', false)->count());
        $this->assertEquals(5, Tenant::byPlan($plan->id)->count());
    }

    /**
     * Test tenant deletion.
     */
    public function test_can_delete_tenant(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->withoutMiddleware()->deleteJson("/api/tenants/{$tenant->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tenant deleted successfully',
            ]);

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }
}
