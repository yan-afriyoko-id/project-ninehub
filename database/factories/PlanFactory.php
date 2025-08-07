<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Free', 'Basic', 'Premium', 'Enterprise']),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomElement([0, 100000, 250000, 500000]),
            'currency' => 'IDR',
            'max_users' => fake()->randomElement([5, 10, 25, 100]),
            'max_storage' => fake()->randomElement([100, 500, 1000, 5000]),
            'features' => ['dashboard'],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the plan is free.
     */
    public function free(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Free',
            'slug' => 'free',
            'price' => 0,
            'max_users' => 2,
            'max_storage' => 50,
            'features' => ['dashboard'],
        ]);
    }

    /**
     * Indicate that the plan is basic.
     */
    public function basic(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Basic',
            'slug' => 'basic',
            'price' => 50000,
            'max_users' => 5,
            'max_storage' => 100,
            'features' => ['dashboard', 'user-management'],
        ]);
    }

    /**
     * Indicate that the plan is premium.
     */
    public function premium(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 150000,
            'max_users' => 20,
            'max_storage' => 500,
            'features' => ['dashboard', 'user-management', 'tenant-management', 'plan-management'],
        ]);
    }

    /**
     * Indicate that the plan is enterprise.
     */
    public function enterprise(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'price' => 500000,
            'max_users' => 100,
            'max_storage' => 2000,
            'features' => ['dashboard', 'user-management', 'tenant-management', 'plan-management', 'settings'],
        ]);
    }
}
