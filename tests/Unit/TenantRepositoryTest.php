<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use App\Repositories\TenantRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TenantRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private TenantRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TenantRepository(new Tenant());
    }

    /**
     * Test getting all tenants
     */
    public function test_get_all_tenants(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Tenant::factory()->count(3)->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $tenants = $this->repository->all();

        $this->assertEquals(3, $tenants->count());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $tenants);
    }

    /**
     * Test finding tenant by ID
     */
    public function test_find_tenant_by_id(): void
    {
        $tenant = Tenant::factory()->create();

        $foundTenant = $this->repository->find($tenant->id);
        $this->assertNotNull($foundTenant);
        $this->assertEquals($tenant->id, $foundTenant->id);

        $notFoundTenant = $this->repository->find(999);
        $this->assertNull($notFoundTenant);
    }

    /**
     * Test finding tenant by ID or fail
     */
    public function test_find_or_fail_tenant(): void
    {
        $tenant = Tenant::factory()->create();

        $foundTenant = $this->repository->findOrFail($tenant->id);
        $this->assertEquals($tenant->id, $foundTenant->id);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->repository->findOrFail(999);
    }

    /**
     * Test creating tenant
     */
    public function test_create_tenant(): void
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

        $tenant = $this->repository->create($tenantData);

        $this->assertNotNull($tenant);
        $this->assertEquals('Test Company', $tenant->name);
        $this->assertEquals($user->id, $tenant->user_id);
        $this->assertEquals($plan->id, $tenant->plan_id);
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

        $updatedTenant = $this->repository->update($tenant->id, $updateData);

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

        $result = $this->repository->delete($tenant->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    /**
     * Test paginating tenants with filters
     */
    public function test_paginate_tenants_with_filters(): void
    {
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
        $tenants = $this->repository->paginate();
        $this->assertEquals(5, $tenants->total());

        // Test with active filter
        $activeTenants = $this->repository->paginate(['is_active' => true]);
        $this->assertEquals(3, $activeTenants->total());

        // Test with search filter
        $searchTenants = $this->repository->paginate(['search' => 'Test']);
        $this->assertGreaterThanOrEqual(0, $searchTenants->total());
    }

    /**
     * Test getting active tenants
     */
    public function test_get_active_tenants(): void
    {
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

        $activeTenants = $this->repository->getActiveTenants();
        $this->assertEquals(3, $activeTenants->count());
    }

    /**
     * Test getting inactive tenants
     */
    public function test_get_inactive_tenants(): void
    {
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

        $inactiveTenants = $this->repository->getInactiveTenants();
        $this->assertEquals(2, $inactiveTenants->count());
    }

    /**
     * Test getting tenants by plan
     */
    public function test_get_tenants_by_plan(): void
    {
        $user = User::factory()->create();
        $plan1 = Plan::factory()->create();
        $plan2 = Plan::factory()->create();

        Tenant::factory()->count(3)->create([
            'user_id' => $user->id,
            'plan_id' => $plan1->id,
        ]);
        Tenant::factory()->count(2)->create([
            'user_id' => $user->id,
            'plan_id' => $plan2->id,
        ]);

        $tenantsByPlan = $this->repository->getTenantsByPlan($plan1->id);
        $this->assertEquals(3, $tenantsByPlan->count());
    }

    /**
     * Test searching tenants
     */
    public function test_search_tenants(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Tenant::factory()->create([
            'name' => 'Test Company',
            'email' => 'admin@testcompany.com',
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
        Tenant::factory()->create([
            'name' => 'Another Company',
            'email' => 'admin@anothercompany.com',
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $searchResults = $this->repository->searchTenants('Test');
        $this->assertEquals(1, $searchResults->count());
        $this->assertEquals('Test Company', $searchResults->first()->name);
    }

    /**
     * Test getting tenant statistics
     */
    public function test_get_tenant_statistics(): void
    {
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

        $stats = $this->repository->getTenantStatistics();

        $this->assertArrayHasKey('total_tenants', $stats);
        $this->assertArrayHasKey('active_tenants', $stats);
        $this->assertArrayHasKey('inactive_tenants', $stats);
        $this->assertArrayHasKey('by_plan', $stats);

        $this->assertEquals(5, $stats['total_tenants']);
        $this->assertEquals(3, $stats['active_tenants']);
        $this->assertEquals(2, $stats['inactive_tenants']);
    }
}
