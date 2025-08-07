<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Services\PlanService;
use App\Services\Interfaces\PlanServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private PlanService $planService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->planService = app(PlanService::class);
    }

    public function test_get_all_plans_with_filters(): void
    {
        Plan::factory()->count(3)->create();

        $plans = $this->planService->getAllPlans(['per_page' => 10]);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $plans);
        $this->assertEquals(3, $plans->total());
    }

    public function test_get_plan_by_id(): void
    {
        $plan = Plan::factory()->create();

        $foundPlan = $this->planService->getPlanById($plan->id);

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

        $plan = $this->planService->createPlan($planData);

        $this->assertInstanceOf(Plan::class, $plan);
        $this->assertEquals('Test Plan', $plan->name);
        $this->assertEquals('test-plan', $plan->slug);
        $this->assertEquals(100000, $plan->price);
    }

    public function test_update_plan(): void
    {
        $plan = Plan::factory()->create();
        $updateData = ['name' => 'Updated Plan', 'price' => 200000];

        $updatedPlan = $this->planService->updatePlan($plan->id, $updateData);

        $this->assertInstanceOf(Plan::class, $updatedPlan);
        $this->assertEquals('Updated Plan', $updatedPlan->name);
        $this->assertEquals(200000, $updatedPlan->price);
    }

    public function test_delete_plan(): void
    {
        $plan = Plan::factory()->create();

        $result = $this->planService->deletePlan($plan->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    public function test_get_active_plans(): void
    {
        Plan::factory()->count(2)->create(['is_active' => true]);
        Plan::factory()->count(3)->create(['is_active' => false]);

        $activePlans = $this->planService->getActivePlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $activePlans);
        $this->assertEquals(2, $activePlans->count());
    }

    public function test_get_free_plans(): void
    {
        Plan::factory()->count(2)->create(['price' => 0]);
        Plan::factory()->count(3)->create(['price' => 100000]);

        $freePlans = $this->planService->getFreePlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $freePlans);
        $this->assertEquals(2, $freePlans->count());
    }

    public function test_get_paid_plans(): void
    {
        Plan::factory()->count(2)->create(['price' => 0]);
        Plan::factory()->count(3)->create(['price' => 100000]);

        $paidPlans = $this->planService->getPaidPlans();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $paidPlans);
        $this->assertEquals(3, $paidPlans->count());
    }

    public function test_search_plans(): void
    {
        Plan::factory()->create(['name' => 'Premium Plan']);
        Plan::factory()->create(['name' => 'Basic Plan']);
        Plan::factory()->create(['name' => 'Free Plan']);

        $searchResults = $this->planService->searchPlans('Premium');

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

        $statistics = $this->planService->getPlanStatistics();

        $this->assertIsArray($statistics);
        $this->assertEquals(5, $statistics['total_plans']);
        $this->assertEquals(2, $statistics['active_plans']);
        $this->assertEquals(1, $statistics['free_plans']);
        $this->assertEquals(4, $statistics['paid_plans']);
    }

    public function test_plan_not_found_exception(): void
    {
        $plan = $this->planService->getPlanById(999);

        $this->assertNull($plan);
    }
}
