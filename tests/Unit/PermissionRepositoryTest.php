<?php

namespace Tests\Unit;

use Spatie\Permission\Models\Permission;
use App\Repositories\PermissionRepository;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private PermissionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PermissionRepository(new Permission());
    }

    /**
     * Test getting all permissions
     */
    public function test_get_all_permissions(): void
    {
        Permission::create(['name' => 'test.permission1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test.permission2', 'guard_name' => 'web']);

        $permissions = $this->repository->all();

        $this->assertEquals(2, $permissions->count());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $permissions);
    }

    /**
     * Test finding permission by ID
     */
    public function test_find_permission_by_id(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $foundPermission = $this->repository->find($permission->id);
        $this->assertNotNull($foundPermission);
        $this->assertEquals($permission->id, $foundPermission->id);

        $notFoundPermission = $this->repository->find(999);
        $this->assertNull($notFoundPermission);
    }

    /**
     * Test finding permission by ID or fail
     */
    public function test_find_or_fail_permission(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $foundPermission = $this->repository->findOrFail($permission->id);
        $this->assertEquals($permission->id, $foundPermission->id);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->repository->findOrFail(999);
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

        $permission = $this->repository->create($permissionData);

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

        $updatedPermission = $this->repository->update($permission->id, $updateData);

        $this->assertEquals('test.updated', $updatedPermission->name);
        $this->assertEquals('api', $updatedPermission->guard_name);
    }

    /**
     * Test deleting permission
     */
    public function test_delete_permission(): void
    {
        $permission = Permission::create(['name' => 'test.permission', 'guard_name' => 'web']);

        $result = $this->repository->delete($permission->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    /**
     * Test paginating permissions with filters
     */
    public function test_paginate_permissions_with_filters(): void
    {
        Permission::create(['name' => 'test.permission1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test.permission2', 'guard_name' => 'web']);
        Permission::create(['name' => 'api.permission1', 'guard_name' => 'api']);

        // Test without filters
        $permissions = $this->repository->paginate();
        $this->assertEquals(3, $permissions->total());

        // Test with guard filter
        $webPermissions = $this->repository->paginate(['guard_name' => 'web']);
        $this->assertEquals(2, $webPermissions->total());

        // Test with search filter
        $searchPermissions = $this->repository->paginate(['search' => 'test']);
        $this->assertEquals(2, $searchPermissions->total());
    }

    /**
     * Test getting permissions by guard
     */
    public function test_get_permissions_by_guard(): void
    {
        Permission::create(['name' => 'test.permission1', 'guard_name' => 'web']);
        Permission::create(['name' => 'test.permission2', 'guard_name' => 'web']);
        Permission::create(['name' => 'api.permission1', 'guard_name' => 'api']);

        $webPermissions = $this->repository->getPermissionsByGuard('web');
        $this->assertEquals(2, $webPermissions->count());

        $apiPermissions = $this->repository->getPermissionsByGuard('api');
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

        $dashboardPermissions = $this->repository->getPermissionsByModule('dashboard');
        $this->assertEquals(2, $dashboardPermissions->count());

        $usersPermissions = $this->repository->getPermissionsByModule('users');
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

        $searchResults = $this->repository->searchPermissions('dashboard');
        $this->assertEquals(2, $searchResults->count());

        $searchResults = $this->repository->searchPermissions('users');
        $this->assertEquals(1, $searchResults->count());
    }

    /**
     * Test getting permission statistics
     */
    public function test_get_permission_statistics(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'api']);

        $stats = $this->repository->getPermissionStatistics();

        $this->assertArrayHasKey('total_permissions', $stats);
        $this->assertArrayHasKey('by_guard', $stats);
        $this->assertArrayHasKey('by_module', $stats);

        $this->assertEquals(3, $stats['total_permissions']);
        $this->assertEquals(2, $stats['by_guard']['web']);
        $this->assertEquals(1, $stats['by_guard']['api']);
        $this->assertEquals(2, $stats['by_module']['dashboard']);
        $this->assertEquals(1, $stats['by_module']['users']);
    }
}
