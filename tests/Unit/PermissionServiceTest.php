<?php

namespace Tests\Unit;

use Spatie\Permission\Models\Permission;
use App\Models\Module;
use App\Services\PermissionService;
use App\Services\Interfaces\PermissionServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private PermissionService $permissionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permissionService = app(PermissionService::class);
    }

    /**
     * Test getting all permissions with filters
     */
    public function test_get_all_permissions_with_filters(): void
    {
        // Create test data
        Permission::create(['name' => 'test.permission1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test.permission2', 'guard_name' => 'web']);
        Permission::create(['name' => 'api.permission1', 'guard_name' => 'api']);

        // Test without filters
        $permissions = $this->permissionService->getAllPermissions();
        $this->assertEquals(3, $permissions->total());

        // Test with guard filter
        $webPermissions = $this->permissionService->getAllPermissions(['guard_name' => 'web']);
        $this->assertEquals(2, $webPermissions->total());

        // Test with search filter
        $searchPermissions = $this->permissionService->getAllPermissions(['search' => 'test']);
        $this->assertEquals(2, $searchPermissions->total());
    }

    /**
     * Test getting permission by ID
     */
    public function test_get_permission_by_id(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $foundPermission = $this->permissionService->getPermissionById($permission->id);
        $this->assertNotNull($foundPermission);
        $this->assertEquals($permission->id, $foundPermission->id);

        $notFoundPermission = $this->permissionService->getPermissionById(999);
        $this->assertNull($notFoundPermission);
    }

    /**
     * Test creating permission
     */
    public function test_create_permission(): void
    {
        $permissionData = [
            'name' => 'test.create',
            'guard_name' => 'web',
        ];

        $permission = $this->permissionService->createPermission($permissionData);

        $this->assertNotNull($permission);
        $this->assertEquals('test.create', $permission->name);
        $this->assertEquals('web', $permission->guard_name);
    }

    /**
     * Test updating permission
     */
    public function test_update_permission(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $updateData = [
            'name' => 'test.updated',
            'guard_name' => 'api',
        ];

        $updatedPermission = $this->permissionService->updatePermission($permission->id, $updateData);

        $this->assertEquals('test.updated', $updatedPermission->name);
        $this->assertEquals('api', $updatedPermission->guard_name);
    }

    /**
     * Test deleting permission
     */
    public function test_delete_permission(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $result = $this->permissionService->deletePermission($permission->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    /**
     * Test getting permissions by guard
     */
    public function test_get_permissions_by_guard(): void
    {
        Permission::create(['name' => 'test.permission1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test.permission2', 'guard_name' => 'web']);
        Permission::create(['name' => 'api.permission1', 'guard_name' => 'api']);

        $webPermissions = $this->permissionService->getPermissionsByGuard('web');
        $this->assertEquals(2, $webPermissions->count());

        $apiPermissions = $this->permissionService->getPermissionsByGuard('api');
        $this->assertEquals(1, $apiPermissions->count());
    }

    /**
     * Test getting permissions by module
     */
    public function test_get_permissions_by_module(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);

        $dashboardPermissions = $this->permissionService->getPermissionsByModule('dashboard');
        $this->assertEquals(2, $dashboardPermissions->count());

        $usersPermissions = $this->permissionService->getPermissionsByModule('users');
        $this->assertEquals(1, $usersPermissions->count());
    }

    /**
     * Test searching permissions
     */
    public function test_search_permissions(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);

        $searchResults = $this->permissionService->searchPermissions('dashboard');
        $this->assertEquals(2, $searchResults->count());

        $searchResults = $this->permissionService->searchPermissions('users');
        $this->assertEquals(1, $searchResults->count());
    }

    /**
     * Test syncing permissions from modules
     */
    public function test_sync_permissions_from_modules(): void
    {
        // Create a module with permissions
        $module = Module::factory()->create([
            'slug' => 'test-module',
            'permissions' => ['view', 'create', 'edit'],
        ]);

        $result = $this->permissionService->syncPermissionsFromModules();

        $this->assertTrue($result);

        // Check if permissions were created
        $this->assertDatabaseHas('permissions', [
            'name' => 'test-module.view',
            'guard_name' => 'web',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'test-module.create',
            'guard_name' => 'web',
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'test-module.edit',
            'guard_name' => 'web',
        ]);
    }

    /**
     * Test getting permission statistics
     */
    public function test_get_permission_statistics(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'api']);

        $stats = $this->permissionService->getPermissionStatistics();

        $this->assertArrayHasKey('total_permissions', $stats);
        $this->assertArrayHasKey('by_guard', $stats);
        $this->assertArrayHasKey('by_module', $stats);

        $this->assertEquals(3, $stats['total_permissions']);
        $this->assertEquals(2, $stats['by_guard']['web']);
        $this->assertEquals(1, $stats['by_guard']['api']);
        $this->assertEquals(2, $stats['by_module']['dashboard']);
        $this->assertEquals(1, $stats['by_module']['users']);
    }

    /**
     * Test permission not found exception
     */
    public function test_permission_not_found_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->permissionService->updatePermission(999, ['name' => 'test']);
    }
}
