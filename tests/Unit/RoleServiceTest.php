<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\Permission;
use App\Services\RoleService;
use App\Services\Interfaces\RoleServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private RoleService $roleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleService = app(RoleService::class);
    }

    public function test_get_all_roles_with_filters(): void
    {
        Role::factory()->count(3)->create();

        $roles = $this->roleService->getAllRoles(['per_page' => 10]);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $roles);
        $this->assertEquals(3, $roles->total());
    }

    public function test_get_role_by_id(): void
    {
        $role = Role::factory()->create();

        $foundRole = $this->roleService->getRoleById($role->id);

        $this->assertInstanceOf(Role::class, $foundRole);
        $this->assertEquals($role->id, $foundRole->id);
    }

    public function test_create_role(): void
    {
        $roleData = [
            'name' => 'test-role',
            'guard_name' => 'api'
        ];

        $role = $this->roleService->createRole($roleData);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('test-role', $role->name);
        $this->assertEquals('api', $role->guard_name);
    }

    public function test_update_role(): void
    {
        $role = Role::factory()->create();
        $updateData = ['name' => 'updated-role'];

        $updatedRole = $this->roleService->updateRole($role->id, $updateData);

        $this->assertInstanceOf(Role::class, $updatedRole);
        $this->assertEquals('updated-role', $updatedRole->name);
    }

    public function test_delete_role(): void
    {
        $role = Role::factory()->create();

        $result = $this->roleService->deleteRole($role->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_get_roles_by_guard(): void
    {
        Role::factory()->count(2)->create(['guard_name' => 'api']);
        Role::factory()->count(3)->create(['guard_name' => 'web']);

        $apiRoles = $this->roleService->getRolesByGuard('api');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $apiRoles);
        $this->assertEquals(2, $apiRoles->count());
    }

    public function test_search_roles(): void
    {
        Role::factory()->create(['name' => 'admin-role']);
        Role::factory()->create(['name' => 'user-role']);
        Role::factory()->create(['name' => 'guest-role']);

        $searchResults = $this->roleService->searchRoles('admin');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $searchResults);
        $this->assertEquals(1, $searchResults->count());
        $this->assertEquals('admin-role', $searchResults->first()->name);
    }

    public function test_assign_permissions_to_role(): void
    {
        $role = Role::factory()->create(['guard_name' => 'api']);
        $permissions = Permission::factory()->count(3)->api()->create();
        $permissionIds = $permissions->pluck('id')->toArray();

        $result = $this->roleService->assignPermissionsToRole($role->id, $permissionIds);

        $this->assertTrue($result);
        $this->assertEquals(3, $role->fresh()->permissions->count());
    }

    public function test_remove_permissions_from_role(): void
    {
        $role = Role::factory()->create(['guard_name' => 'api']);
        $permissions = Permission::factory()->count(3)->api()->create();
        $role->givePermissionTo($permissions);

        $permissionIds = $permissions->take(2)->pluck('id')->toArray();

        $result = $this->roleService->removePermissionsFromRole($role->id, $permissionIds);

        $this->assertTrue($result);
        $this->assertEquals(1, $role->fresh()->permissions->count());
    }

    public function test_get_role_statistics(): void
    {
        Role::factory()->count(2)->create(['guard_name' => 'api']);
        Role::factory()->count(3)->create(['guard_name' => 'web']);

        $statistics = $this->roleService->getRoleStatistics();

        $this->assertIsArray($statistics);
        $this->assertEquals(5, $statistics['total_roles']);
        $this->assertEquals(2, $statistics['by_guard']['api']);
        $this->assertEquals(3, $statistics['by_guard']['web']);
    }

    public function test_role_not_found_exception(): void
    {
        $role = $this->roleService->getRoleById(999);

        $this->assertNull($role);
    }
}
