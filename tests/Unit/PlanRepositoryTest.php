<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Repositories\PlanRepository;
use App\Repositories\Interfaces\PlanRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private PlanRepository $planRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->planRepository = app(PlanRepository::class);
    }

    public function test_get_all_plans(): void
    {
        Plan::factory()->count(3)->create();

        $plans = $this->planRepository->all();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $plans);
        $this->assertEquals(3, $plans->count());
    }

    public function test_find_plan_by_id(): void
    {
        $plan = Plan::factory()->create();

        $foundPlan = $this->planRepository->find($plan->id);

        $this->assertInstanceOf(Plan::class, $foundPlan);
        $this->assertEquals($plan->id, $foundPlan->id);
    }

    public function test_find_or_fail_plan(): void
    {
        $plan = Plan::factory()->create();

        $foundPlan = $this->planRepository->findOrFail($plan->id);

        $this->assertInstanceOf(Plan::class, $foundPlan);
        $this->assertEquals($plan->id, $foundPlan->id);
    }

    public function test_create_plan(): void
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

        $plan = $this->planRepository->create($planData);

        $this->assertInstanceOf(Plan::class, $plan);
        $this->assertEquals('Test Plan', $plan->name);
        $this->assertEquals('test-plan', $plan->slug);
        $this->assertEquals(100000, $plan->price);
    }

    public function test_update_plan(): void
    {
        $plan = Plan::factory()->create();
        $updateData = ['name' => 'Updated Plan', 'price' => 200000];

        $updatedPlan = $this->planRepository->update($plan->id, $updateData);

        $this->assertInstanceOf(Plan::class, $updatedPlan);
        $this->assertEquals('Updated Plan', $updatedPlan->name);
        $this->assertEquals(200000, $updatedPlan->price);
    }

    public function test_delete_plan(): void
    {
        $plan = Plan::factory()->create();

        $result = $this->planRepository->delete($plan->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    public function test_paginate_plans_with_filters(): void
    {
        Plan::factory()->count(5)->create();

        $plans = $this->planRepository->paginate(['per_page' => 3]);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $plans);
        $this->assertEquals(5, $plans->total());
        $this->assertEquals(3, $plans->perPage());
    }

    public function test_get_active_plans(): void
    {
        Plan::factory()->count(2)->create(['is_active' => true]);
        Plan::factory()->count(3)->create(['is_active' => false]);

        $activePlans = $this->planRepository->getActivePlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $activePlans);
        $this->assertEquals(2, $activePlans->count());
    }

    public function test_get_free_plans(): void
    {
        Plan::factory()->count(2)->create(['price' => 0]);
        Plan::factory()->count(3)->create(['price' => 100000]);

        $freePlans = $this->planRepository->getFreePlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $freePlans);
        $this->assertEquals(2, $freePlans->count());
    }

    public function test_get_paid_plans(): void
    {
        Plan::factory()->count(2)->create(['price' => 0]);
        Plan::factory()->count(3)->create(['price' => 100000]);

        $paidPlans = $this->planRepository->getPaidPlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $paidPlans);
        $this->assertEquals(3, $paidPlans->count());
    }

    public function test_search_plans(): void
    {
        Plan::factory()->create(['name' => 'Premium Plan']);
        Plan::factory()->create(['name' => 'Basic Plan']);
        Plan::factory()->create(['name' => 'Free Plan']);

        $searchResults = $this->planRepository->searchPlans('Premium');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $searchResults);
        $this->assertEquals(1, $searchResults->count());
        $this->assertEquals('Premium Plan', $searchResults->first()->name);
    }

    public function test_get_plan_statistics(): void
    {
        Plan::factory()->count(2)->create(['is_active' => true]);
        Plan::factory()->count(3)->create(['is_active' => false]);
        Plan::factory()->count(1)->create(['price' => 0]);
        Plan::factory()->count(4)->create(['price' => 100000]);

        $statistics = $this->planRepository->getPlanStatistics();

        $this->assertIsArray($statistics);
        $this->assertEquals(5, $statistics['total_plans']);
        $this->assertEquals(2, $statistics['active_plans']);
        $this->assertEquals(1, $statistics['free_plans']);
        $this->assertEquals(4, $statistics['paid_plans']);
        $this->assertArrayHasKey('price_ranges', $statistics);
    }

    public function test_plan_relationships_loaded(): void
    {
        $plan = Plan::factory()->create();

        $foundPlan = $this->planRepository->find($plan->id);

        $this->assertInstanceOf(Plan::class, $foundPlan);
        $this->assertTrue(method_exists($foundPlan, 'tenants'));
    }
}
