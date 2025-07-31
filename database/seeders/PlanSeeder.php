<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default plans
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'description' => 'Plan gratis untuk penggunaan dasar',
            'price' => 0,
            'currency' => 'IDR',
            'max_users' => 2,
            'max_storage' => 50,
            'features' => ['dashboard'],
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'Plan dasar untuk bisnis kecil',
            'price' => 50000,
            'currency' => 'IDR',
            'max_users' => 5,
            'max_storage' => 100,
            'features' => ['dashboard', 'user-management'],
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'description' => 'Plan premium untuk bisnis menengah',
            'price' => 150000,
            'currency' => 'IDR',
            'max_users' => 20,
            'max_storage' => 500,
            'features' => ['dashboard', 'user-management', 'tenant-management', 'plan-management'],
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'Plan enterprise untuk bisnis besar',
            'price' => 500000,
            'currency' => 'IDR',
            'max_users' => 100,
            'max_storage' => 2000,
            'features' => ['dashboard', 'user-management', 'tenant-management', 'plan-management', 'settings'],
            'is_active' => true,
        ]);
    }
}
