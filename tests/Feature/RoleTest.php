<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_create_role(): void
    {
        $roleData = [
            'name' => 'test-role',
            'guard_name' => 'api'
        ];

        $response = $this->withoutMiddleware()
            ->postJson('/api/roles', $roleData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'guard_name',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('roles', $roleData);
    }

    public function test_can_list_roles(): void
    {
        Role::factory()->count(3)->create();

        $response = $this->withoutMiddleware()
            ->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_can_get_role_details(): void
    {
        $role = Role::factory()->create();

        $response = $this->withoutMiddleware()
            ->getJson("/api/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'guard_name',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertEquals($role->id, $response->json('data.id'));
    }

    public function test_can_update_role(): void
    {
        $role = Role::factory()->create();
        $updateData = [
            'name' => 'updated-role',
            'guard_name' => 'api'
        ];

        $response = $this->withoutMiddleware()
            ->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'guard_name',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('roles', $updateData);
    }

    public function test_can_delete_role(): void
    {
        $role = Role::factory()->create();

        $response = $this->withoutMiddleware()
            ->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_role_validation(): void
    {
        $response = $this->withoutMiddleware()
            ->postJson('/api/roles', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'guard_name']);
    }

    public function test_role_not_found(): void
    {
        $response = $this->withoutMiddleware()
            ->getJson('/api/roles/999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message'
            ]);
    }

    public function test_can_get_roles_by_guard(): void
    {
        Role::factory()->count(2)->create(['guard_name' => 'api']);
        Role::factory()->count(3)->create(['guard_name' => 'web']);

        $response = $this->withoutMiddleware()
            ->getJson('/api/roles/guard/api');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_can_search_roles(): void
    {
        Role::factory()->create(['name' => 'admin-role']);
        Role::factory()->create(['name' => 'user-role']);
        Role::factory()->create(['name' => 'guest-role']);

        $response = $this->withoutMiddleware()
            ->getJson('/api/roles/search?q=admin');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('admin-role', $response->json('data.0.name'));
    }

    public function test_can_assign_permissions_to_role(): void
    {
        $role = Role::factory()->create(['guard_name' => 'api']);
        $permissions = Permission::factory()->count(3)->api()->create();
        $permissionIds = $permissions->pluck('id')->toArray();

        $response = $this->withoutMiddleware()
            ->postJson("/api/roles/{$role->id}/assign-permissions", [
                'permission_ids' => $permissionIds
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertEquals(3, $role->fresh()->permissions->count());
    }

    public function test_can_remove_permissions_from_role(): void
    {
        $role = Role::factory()->create(['guard_name' => 'api']);
        $permissions = Permission::factory()->count(3)->api()->create();
        $role->givePermissionTo($permissions);

        $permissionIds = $permissions->take(2)->pluck('id')->toArray();

        $response = $this->withoutMiddleware()
            ->postJson("/api/roles/{$role->id}/remove-permissions", [
                'permission_ids' => $permissionIds
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertEquals(1, $role->fresh()->permissions->count());
    }

    public function test_can_get_role_statistics(): void
    {
        Role::factory()->count(2)->create(['guard_name' => 'api']);
        Role::factory()->count(3)->create(['guard_name' => 'web']);

        $response = $this->withoutMiddleware()
            ->getJson('/api/roles/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_roles',
                    'by_guard'
                ]
            ]);

        $this->assertEquals(5, $response->json('data.total_roles'));
        $this->assertEquals(2, $response->json('data.by_guard.api'));
        $this->assertEquals(3, $response->json('data.by_guard.web'));
    }
}
