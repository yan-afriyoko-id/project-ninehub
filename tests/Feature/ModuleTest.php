<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test module creation.
     */
    public function test_can_create_module(): void
    {
        $moduleData = [
            'name' => 'Test Module',
            'slug' => 'test-module',
            'description' => 'Test module description',
            'icon' => 'test-icon',
            'route' => 'test.route',
            'order' => 1,
            'is_active' => true,
            'is_public' => false,
            'permissions' => ['view', 'create', 'edit'],
        ];

        $response = $this->withoutMiddleware()->postJson('/api/modules', $moduleData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'icon',
                    'route',
                    'order',
                    'is_active',
                    'is_public',
                    'permissions',
                    'created_at',
                    'updated_at',
                    'tenants_count',
                    'permissions_to_create',
                ]
            ]);

        $this->assertDatabaseHas('modules', [
            'name' => 'Test Module',
            'slug' => 'test-module',
            'is_active' => true,
            'is_public' => false,
        ]);
    }

    /**
     * Test module listing.
     */
    public function test_can_list_modules(): void
    {
        // Clear database first
        Module::query()->delete();
        User::query()->delete();

        // Create test data
        Module::factory()->count(3)->active()->create();

        $response = $this->withoutMiddleware()->getJson('/api/modules');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'icon',
                        'route',
                        'order',
                        'is_active',
                        'is_public',
                        'permissions',
                        'created_at',
                        'updated_at',
                        'tenants_count',
                        'permissions_to_create',
                    ]
                ],
                'message'
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    /**
     * Test module details.
     */
    public function test_can_get_module_details(): void
    {
        $module = Module::factory()->create();

        $response = $this->withoutMiddleware()->getJson("/api/modules/{$module->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'icon',
                    'route',
                    'order',
                    'is_active',
                    'is_public',
                    'permissions',
                    'created_at',
                    'updated_at',
                    'tenants_count',
                    'permissions_to_create',
                ]
            ]);
    }

    /**
     * Test module update.
     */
    public function test_can_update_module(): void
    {
        $module = Module::factory()->create();

        $updateData = [
            'name' => 'Updated Module',
            'description' => 'Updated description',
            'is_active' => false,
        ];

        $response = $this->withoutMiddleware()->putJson("/api/modules/{$module->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Module updated successfully',
            ]);

        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'name' => 'Updated Module',
            'description' => 'Updated description',
            'is_active' => false,
        ]);
    }

    /**
     * Test module deletion.
     */
    public function test_can_delete_module(): void
    {
        $module = Module::factory()->create();

        $response = $this->withoutMiddleware()->deleteJson("/api/modules/{$module->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Module deleted successfully',
            ]);

        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
    }

    /**
     * Test module validation.
     */
    public function test_module_validation(): void
    {
        // Test required fields
        $response = $this->withoutMiddleware()->postJson('/api/modules', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);

        // Test unique slug
        Module::factory()->create(['slug' => 'existing-module']);

        $response = $this->withoutMiddleware()->postJson('/api/modules', [
            'name' => 'Test Module',
            'slug' => 'existing-module',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * Test module not found.
     */
    public function test_module_not_found(): void
    {
        $response = $this->withoutMiddleware()->getJson('/api/modules/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Module not found',
            ]);
    }

    /**
     * Test module model methods.
     */
    public function test_module_model_methods(): void
    {
        $module = Module::factory()->create([
            'permissions' => ['view', 'create', 'edit'],
        ]);

        // Test getModulePermissions
        $this->assertEquals(['view', 'create', 'edit'], $module->getModulePermissions());

        // Test hasPermission
        $this->assertTrue($module->hasPermission('view'));
        $this->assertFalse($module->hasPermission('delete'));

        // Test getPermissionsToCreate
        $expectedPermissions = [
            $module->slug . '.view',
            $module->slug . '.create',
            $module->slug . '.edit',
        ];
        $this->assertEquals($expectedPermissions, $module->getPermissionsToCreate());
    }

    /**
     * Test module scopes.
     */
    public function test_module_scopes(): void
    {
        // Clear database first
        Module::query()->delete();

        Module::factory()->count(3)->active()->create();
        Module::factory()->count(2)->inactive()->create();
        Module::factory()->count(2)->inactive()->public()->create();

        $this->assertEquals(3, Module::active()->count());
        $this->assertEquals(4, Module::where('is_active', false)->count());
        $this->assertEquals(2, Module::public()->count());
    }
}
