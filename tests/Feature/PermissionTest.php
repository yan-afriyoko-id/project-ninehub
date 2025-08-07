<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test permission creation.
     */
    public function test_can_create_permission(): void
    {
        $permissionData = [
            'name' => 'test.permission',
            'guard_name' => 'web',
        ];

        $response = $this->withoutMiddleware()->postJson('/api/permissions', $permissionData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'guard_name',
                    'created_at',
                    'updated_at',
                    'module',
                    'action',
                    'roles_count',
                ]
            ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'test.permission',
            'guard_name' => 'web',
        ]);
    }

    /**
     * Test permission listing.
     */
    public function test_can_list_permissions(): void
    {
        // Clear database first
        Permission::query()->delete();

        // Create test data
        Permission::create(['name' => 'test.permission1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test.permission2', 'guard_name' => 'web']);
        Permission::create(['name' => 'api.permission1', 'guard_name' => 'api']);

        $response = $this->withoutMiddleware()->getJson('/api/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'created_at',
                        'updated_at',
                        'module',
                        'action',
                        'roles_count',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    /**
     * Test permission details.
     */
    public function test_can_get_permission_details(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $response = $this->withoutMiddleware()->getJson("/api/permissions/{$permission->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'guard_name',
                    'created_at',
                    'updated_at',
                    'module',
                    'action',
                    'roles_count',
                ]
            ]);
    }

    /**
     * Test permission update.
     */
    public function test_can_update_permission(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $updateData = [
            'name' => 'test.updated',
            'guard_name' => 'api',
        ];

        $response = $this->withoutMiddleware()->putJson("/api/permissions/{$permission->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Permission updated successfully',
            ]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'test.updated',
            'guard_name' => 'api',
        ]);
    }

    /**
     * Test permission deletion.
     */
    public function test_can_delete_permission(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $response = $this->withoutMiddleware()->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Permission deleted successfully',
            ]);

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    /**
     * Test permission validation.
     */
    public function test_permission_validation(): void
    {
        // Test required fields
        $response = $this->withoutMiddleware()->postJson('/api/permissions', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'guard_name']);

        // Test unique name
        Permission::create(['name' => 'existing.permission', 'guard_name' => 'web']);

        $response = $this->withoutMiddleware()->postJson('/api/permissions', [
            'name' => 'existing.permission',
            'guard_name' => 'web',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Test guard_name validation
        $response = $this->withoutMiddleware()->postJson('/api/permissions', [
            'name' => 'test.permission',
            'guard_name' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['guard_name']);
    }

    /**
     * Test permission not found.
     */
    public function test_permission_not_found(): void
    {
        $response = $this->withoutMiddleware()->getJson('/api/permissions/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Permission not found',
            ]);
    }

    /**
     * Test getting permissions by guard.
     */
    public function test_can_get_permissions_by_guard(): void
    {
        Permission::create(['name' => 'test.permission1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test.permission2', 'guard_name' => 'web']);
        Permission::create(['name' => 'api.permission1', 'guard_name' => 'api']);

        $response = $this->withoutMiddleware()->getJson('/api/permissions/guard/web');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'created_at',
                        'updated_at',
                        'module',
                        'action',
                        'roles_count',
                    ]
                ],
                'message'
            ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    /**
     * Test getting permissions by module.
     */
    public function test_can_get_permissions_by_module(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);

        $response = $this->withoutMiddleware()->getJson('/api/permissions/module/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'created_at',
                        'updated_at',
                        'module',
                        'action',
                        'roles_count',
                    ]
                ],
                'message'
            ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    /**
     * Test searching permissions.
     */
    public function test_can_search_permissions(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);

        $response = $this->withoutMiddleware()->getJson('/api/permissions/search?q=dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'created_at',
                        'updated_at',
                        'module',
                        'action',
                        'roles_count',
                    ]
                ],
                'message'
            ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    /**
     * Test syncing permissions.
     */
    public function test_can_sync_permissions(): void
    {
        $response = $this->withoutMiddleware()->postJson('/api/permissions/sync');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Permissions synced successfully',
            ]);
    }

    /**
     * Test getting permission statistics.
     */
    public function test_can_get_permission_statistics(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'api']);

        $response = $this->withoutMiddleware()->getJson('/api/permissions/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_permissions',
                    'by_guard',
                    'by_module',
                ],
                'message'
            ]);

        $data = $response->json('data');
        $this->assertEquals(3, $data['total_permissions']);
        $this->assertEquals(2, $data['by_guard']['web']);
        $this->assertEquals(1, $data['by_guard']['api']);
    }
}
