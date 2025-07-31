<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => str_replace(' ', '-', strtolower($name)),
            'description' => $this->faker->sentence(),
            'icon' => $this->faker->randomElement(['dashboard', 'users', 'settings', 'reports', 'analytics']),
            'route' => $this->faker->randomElement(['dashboard', 'users.index', 'settings.index', 'reports.index']),
            'order' => $this->faker->numberBetween(1, 10),
            'is_active' => $this->faker->boolean(80),
            'is_public' => $this->faker->boolean(20),
            'permissions' => $this->faker->randomElements([
                'view',
                'create',
                'edit',
                'delete',
                'export',
                'import'
            ], $this->faker->numberBetween(1, 4)),
        ];
    }

    /**
     * Indicate that the module is public.
     */
    public function public(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_public' => true,
            'permissions' => ['view'],
        ]);
    }

    /**
     * Indicate that the module is private.
     */
    public function private(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_public' => false,
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ]);
    }
}
