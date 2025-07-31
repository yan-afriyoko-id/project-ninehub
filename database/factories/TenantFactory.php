<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'logo' => null,
            'user_id' => User::factory(),
            'plan_id' => Plan::inRandomOrder()->first()?->id ?? Plan::factory(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the tenant is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the tenant is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the tenant uses free plan.
     */
    public function free(): static
    {
        return $this->state(fn(array $attributes) => [
            'plan_id' => Plan::where('slug', 'free')->first()?->id ?? Plan::factory()->free(),
        ]);
    }

    /**
     * Indicate that the tenant uses basic plan.
     */
    public function basic(): static
    {
        return $this->state(fn(array $attributes) => [
            'plan_id' => Plan::where('slug', 'basic')->first()?->id ?? Plan::factory()->basic(),
        ]);
    }

    /**
     * Indicate that the tenant uses premium plan.
     */
    public function premium(): static
    {
        return $this->state(fn(array $attributes) => [
            'plan_id' => Plan::where('slug', 'premium')->first()?->id ?? Plan::factory()->premium(),
        ]);
    }

    /**
     * Indicate that the tenant uses enterprise plan.
     */
    public function enterprise(): static
    {
        return $this->state(fn(array $attributes) => [
            'plan_id' => Plan::where('slug', 'enterprise')->first()?->id ?? Plan::factory()->enterprise(),
        ]);
    }
}
