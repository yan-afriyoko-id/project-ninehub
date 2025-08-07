<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'guard_name' => $this->faker->randomElement(['web', 'api']),
        ];
    }

    /**
     * Indicate that the role is for web guard.
     */
    public function web(): static
    {
        return $this->state(fn(array $attributes) => [
            'guard_name' => 'web',
        ]);
    }

    /**
     * Indicate that the role is for api guard.
     */
    public function api(): static
    {
        return $this->state(fn(array $attributes) => [
            'guard_name' => 'api',
        ]);
    }

    /**
     * Create a role with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => $name,
        ]);
    }
}
