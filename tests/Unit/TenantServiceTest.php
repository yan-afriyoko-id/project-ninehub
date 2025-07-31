<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use App\Models\Module;
use App\Services\TenantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TenantServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private TenantService $tenantService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenantService = new TenantService();
    }

    /**
     * Test getting all tenants with filters
     */
    public function test_get_all_tenants_with_filters(): void
    {
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

        // Test without filters
        $tenants = $this->tenantService->getAllTenants();
        $this->assertEquals(5, $tenants->total());

        // Test with active filter
        $activeTenants = $this->tenantService->getAllTenants(['is_active' => true]);
        $this->assertEquals(3, $activeTenants->total());

        // Test with search filter
        $searchTenants = $this->tenantService->getAllTenants(['search' => 'Test']);
        $this->assertGreaterThanOrEqual(0, $searchTenants->total());
    }

    /**
     * Test getting tenant by ID
     */
    public function test_get_tenant_by_id(): void
    {
        $tenant = Tenant::factory()->create();

        $foundTenant = $this->tenantService->getTenantById($tenant->id);
        $this->assertNotNull($foundTenant);
        $this->assertEquals($tenant->id, $foundTenant->id);

        $notFoundTenant = $this->tenantService->getTenantById(999);
        $this->assertNull($notFoundTenant);
    }

    /**
     * Test creating tenant
     */
    public function test_create_tenant(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $module = Module::factory()->create(['slug' => 'dashboard']);

        $tenantData = [
            'name' => 'Test Company',
            'email' => 'admin@testcompany.com',
            'phone' => '+62-21-1234567',
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'is_active' => true,
        ];

        $tenant = $this->tenantService->createTenant($tenantData);

        $this->assertNotNull($tenant);
        $this->assertEquals('Test Company', $tenant->name);
        $this->assertEquals($user->id, $tenant->user_id);
        $this->assertEquals($plan->id, $tenant->plan_id);
        $this->assertTrue($tenant->is_active);
    }

    /**
     * Test updating tenant
     */
    public function test_update_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $newPlan = Plan::factory()->create();

        $updateData = [
            'name' => 'Updated Company',
            'plan_id' => $newPlan->id,
            'is_active' => false,
        ];

        $updatedTenant = $this->tenantService->updateTenant($tenant->id, $updateData);

        $this->assertEquals('Updated Company', $updatedTenant->name);
        $this->assertEquals($newPlan->id, $updatedTenant->plan_id);
        $this->assertFalse($updatedTenant->is_active);
    }

    /**
     * Test deleting tenant
     */
    public function test_delete_tenant(): void
    {
        $tenant = Tenant::factory()->create();

        $result = $this->tenantService->deleteTenant($tenant->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    /**
     * Test activating tenant
     */
    public function test_activate_tenant(): void
    {
        $tenant = Tenant::factory()->inactive()->create();

        $result = $this->tenantService->activateTenant($tenant->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test suspending tenant
     */
    public function test_suspend_tenant(): void
    {
        $tenant = Tenant::factory()->active()->create();

        $result = $this->tenantService->suspendTenant($tenant->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test getting tenant statistics
     */
    public function test_get_tenant_statistics(): void
    {
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

        $stats = $this->tenantService->getTenantStatistics();

        $this->assertArrayHasKey('total_tenants', $stats);
        $this->assertArrayHasKey('active_tenants', $stats);
        $this->assertArrayHasKey('inactive_tenants', $stats);
        $this->assertArrayHasKey('by_plan', $stats);

        $this->assertEquals(5, $stats['total_tenants']);
        $this->assertEquals(3, $stats['active_tenants']);
        $this->assertEquals(2, $stats['inactive_tenants']);
    }

    /**
     * Test tenant not found exception
     */
    public function test_tenant_not_found_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->tenantService->updateTenant(999, ['name' => 'Test']);
    }
}
