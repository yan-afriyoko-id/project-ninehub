<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test plan creation.
     */
    public function test_can_create_plan(): void
    {
        $planData = [
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'description' => 'Test plan description',
            'price' => 100000,
            'currency' => 'IDR',
            'max_users' => 10,
            'max_storage' => 500,
            'features' => ['dashboard', 'user-management'],
            'is_active' => true,
        ];

        $response = $this->withoutMiddleware()
            ->postJson('/api/plans', $planData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'currency',
                    'max_users',
                    'max_storage',
                    'features',
                    'is_active',
                    'formatted_price',
                    'is_free',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('plans', [
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 100000,
            'is_active' => true,
        ]);
    }

    /**
     * Test plan listing.
     */
    public function test_can_list_plans(): void
    {
        Plan::factory()->count(3)->create();

        $response = $this->withoutMiddleware()
            ->getJson('/api/plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'price',
                        'currency',
                        'max_users',
                        'max_storage',
                        'features',
                        'is_active',
                        'formatted_price',
                        'is_free',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    /**
     * Test plan details.
     */
    public function test_can_get_plan_details(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->withoutMiddleware()
            ->getJson("/api/plans/{$plan->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'currency',
                    'max_users',
                    'max_storage',
                    'features',
                    'is_active',
                    'formatted_price',
                    'is_free',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertEquals($plan->id, $response->json('data.id'));
    }

    /**
     * Test plan update.
     */
    public function test_can_update_plan(): void
    {
        $plan = Plan::factory()->create();

        $updateData = [
            'name' => 'Updated Plan',
            'description' => 'Updated description',
            'price' => 200000,
            'is_active' => false,
        ];

        $response = $this->withoutMiddleware()
            ->putJson("/api/plans/{$plan->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Plan updated successfully',
            ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'description' => 'Updated description',
            'price' => 200000,
            'is_active' => false,
        ]);
    }

    /**
     * Test plan deletion.
     */
    public function test_can_delete_plan(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->withoutMiddleware()
            ->deleteJson("/api/plans/{$plan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Plan deleted successfully',
            ]);

        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    /**
     * Test plan validation.
     */
    public function test_plan_validation(): void
    {
        // Test required fields
        $response = $this->withoutMiddleware()
            ->postJson('/api/plans', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'price', 'currency', 'max_users', 'max_storage']);

        // Test unique slug
        Plan::factory()->create(['slug' => 'existing-plan']);

        $response = $this->withoutMiddleware()
            ->postJson('/api/plans', [
                'name' => 'Test Plan',
                'slug' => 'existing-plan',
                'price' => 100000,
                'currency' => 'IDR',
                'max_users' => 10,
                'max_storage' => 500,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * Test plan not found.
     */
    public function test_plan_not_found(): void
    {
        $response = $this->withoutMiddleware()
            ->getJson('/api/plans/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Plan not found',
            ]);
    }

    /**
     * Test plan model methods.
     */
    public function test_plan_model_methods(): void
    {
        $plan = Plan::factory()->create([
            'price' => 0,
            'is_active' => true,
        ]);

        // Test isActive
        $this->assertTrue($plan->isActive());

        // Test isFree
        $this->assertTrue($plan->isFree());

        // Test formatted price
        $this->assertStringContainsString('0', $plan->formatted_price);
        $this->assertStringContainsString('IDR', $plan->formatted_price);

        // Test paid plan
        $paidPlan = Plan::factory()->create(['price' => 100000]);
        $this->assertFalse($paidPlan->isFree());
    }

    /**
     * Test plan scopes.
     */
    public function test_plan_scopes(): void
    {
        // Clear database first
        Plan::query()->delete();

        // Create plans with specific attributes to avoid overlap
        Plan::factory()->count(2)->create(['is_active' => true, 'price' => 50000]);
        Plan::factory()->count(3)->create(['is_active' => false, 'price' => 75000]);
        Plan::factory()->count(2)->create(['is_active' => true, 'price' => 0]);
        Plan::factory()->count(3)->create(['is_active' => false, 'price' => 100000]);

        $this->assertEquals(4, Plan::active()->count()); // 2 active with price + 2 active free
        $this->assertEquals(2, Plan::free()->count()); // Only the 2 with price = 0
        $this->assertEquals(8, Plan::paid()->count()); // All plans except the 2 free ones
    }

    /**
     * Test get active plans.
     */
    public function test_can_get_active_plans(): void
    {
        Plan::factory()->count(2)->create(['is_active' => true]);
        Plan::factory()->count(3)->create(['is_active' => false]);

        $response = $this->withoutMiddleware()
            ->getJson('/api/plans/active');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'is_active',
                    ]
                ]
            ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    /**
     * Test get free plans.
     */
    public function test_can_get_free_plans(): void
    {
        Plan::factory()->count(2)->create(['price' => 0]);
        Plan::factory()->count(3)->create(['price' => 100000]);

        $response = $this->withoutMiddleware()
            ->getJson('/api/plans/free');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'is_free',
                    ]
                ]
            ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    /**
     * Test get paid plans.
     */
    public function test_can_get_paid_plans(): void
    {
        Plan::factory()->count(2)->create(['price' => 0]);
        Plan::factory()->count(3)->create(['price' => 100000]);

        $response = $this->withoutMiddleware()
            ->getJson('/api/plans/paid');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'is_free',
                    ]
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    /**
     * Test search plans.
     */
    public function test_can_search_plans(): void
    {
        Plan::factory()->create(['name' => 'Premium Plan']);
        Plan::factory()->create(['name' => 'Basic Plan']);
        Plan::factory()->create(['name' => 'Free Plan']);

        $response = $this->withoutMiddleware()
            ->getJson('/api/plans/search?q=Premium');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                    ]
                ]
            ]);

        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('Premium Plan', $response->json('data.0.name'));
    }

    /**
     * Test get plan statistics.
     */
    public function test_can_get_plan_statistics(): void
    {
        // Clear database first to ensure clean state
        Plan::query()->delete();

        // Create plans with specific attributes to avoid overlap
        Plan::factory()->count(2)->create(['is_active' => true, 'price' => 50000]);
        Plan::factory()->count(3)->create(['is_active' => false, 'price' => 75000]);
        Plan::factory()->count(1)->create(['is_active' => true, 'price' => 0]);
        Plan::factory()->count(4)->create(['is_active' => false, 'price' => 100000]);

        $response = $this->withoutMiddleware()
            ->getJson('/api/plans/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_plans',
                    'active_plans',
                    'free_plans',
                    'paid_plans',
                    'price_ranges',
                ]
            ]);

        $this->assertEquals(10, $response->json('data.total_plans')); // 2+3+1+4 = 10
        $this->assertEquals(3, $response->json('data.active_plans')); // 2 active + 1 active free
        $this->assertEquals(1, $response->json('data.free_plans')); // Only the 1 with price = 0
        $this->assertEquals(9, $response->json('data.paid_plans')); // All except the 1 free
    }
}
