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

        // If plans don't exist, create them first
        if (!$freePlan) {
            $freePlan = Plan::create([
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Paket gratis dengan fitur terbatas',
                'price' => 0,
                'currency' => 'IDR',
                'max_users' => 2,
                'max_storage' => 50,
                'features' => ['basic_dashboard'],
                'is_active' => true,
            ]);
        }

        if (!$basicPlan) {
            $basicPlan = Plan::create([
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Paket dasar untuk bisnis kecil',
                'price' => 50000,
                'currency' => 'IDR',
                'max_users' => 5,
                'max_storage' => 100,
                'features' => ['basic_dashboard', 'user_management'],
                'is_active' => true,
            ]);
        }

        if (!$premiumPlan) {
            $premiumPlan = Plan::create([
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Paket premium untuk bisnis menengah',
                'price' => 150000,
                'currency' => 'IDR',
                'max_users' => 20,
                'max_storage' => 500,
                'features' => ['basic_dashboard', 'user_management', 'advanced_analytics'],
                'is_active' => true,
            ]);
        }

        if (!$enterprisePlan) {
            $enterprisePlan = Plan::create([
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Paket enterprise untuk bisnis besar',
                'price' => 500000,
                'currency' => 'IDR',
                'max_users' => 100,
                'max_storage' => 2000,
                'features' => ['basic_dashboard', 'user_management', 'advanced_analytics', 'custom_integration'],
                'is_active' => true,
            ]);
        }

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
