<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing plans
        $freePlan = Plan::where('slug', 'free')->first();
        $basicPlan = Plan::where('slug', 'basic')->first();
        $premiumPlan = Plan::where('slug', 'premium')->first();
        $enterprisePlan = Plan::where('slug', 'enterprise')->first();

        // Create sample tenants with different plans
        Tenant::factory()->count(5)->free()->active()->create();
        Tenant::factory()->count(3)->basic()->active()->create();
        Tenant::factory()->count(2)->premium()->active()->create();
        Tenant::factory()->count(1)->enterprise()->active()->create();

        // Create some inactive tenants
        Tenant::factory()->count(2)->inactive()->create();

        // Create a demo tenant for testing
        $demoUser = User::create([
            'name' => 'Demo Owner',
            'email' => 'demo@ninehub.local',
            'password' => bcrypt('password'),
        ]);

        Tenant::create([
            'name' => 'Demo Company',
            'email' => 'admin@demo.ninehub.local',
            'phone' => '+62-21-1234567',
            'logo' => null,
            'user_id' => $demoUser->id,
            'plan_id' => $premiumPlan->id,
            'is_active' => true,
        ]);
    }
}
