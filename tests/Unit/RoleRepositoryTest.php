<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private RoleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(RoleRepository::class);
    }

    public function test_get_all_roles(): void
    {
        Role::factory()->count(3)->create();

        $roles = $this->repository->all();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $roles);
        $this->assertEquals(3, $roles->count());
    }

    public function test_find_role_by_id(): void
    {
        $role = Role::factory()->create();

        $foundRole = $this->repository->find($role->id);

        $this->assertInstanceOf(Role::class, $foundRole);
        $this->assertEquals($role->id, $foundRole->id);
    }

    public function test_find_or_fail_role(): void
    {
        $role = Role::factory()->create();

        $foundRole = $this->repository->findOrFail($role->id);

        $this->assertInstanceOf(Role::class, $foundRole);
        $this->assertEquals($role->id, $foundRole->id);
    }

    public function test_create_role(): void
    {
        $roleData = [
            'name' => 'test-role',
            'guard_name' => 'web'
        ];

        $role = $this->repository->create($roleData);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('test-role', $role->name);
        $this->assertEquals('web', $role->guard_name);
    }

    public function test_update_role(): void
    {
        $role = Role::factory()->create();
        $updateData = ['name' => 'updated-role'];

        $updatedRole = $this->repository->update($role->id, $updateData);

        $this->assertInstanceOf(Role::class, $updatedRole);
        $this->assertEquals('updated-role', $updatedRole->name);
    }

    public function test_delete_role(): void
    {
        $role = Role::factory()->create();

        $result = $this->repository->delete($role->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_paginate_roles_with_filters(): void
    {
        Role::factory()->count(5)->create(['guard_name' => 'web']);
        Role::factory()->count(3)->create(['guard_name' => 'api']);

        $roles = $this->repository->paginate(['guard' => 'web', 'per_page' => 10]);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $roles);
        $this->assertEquals(5, $roles->total());
    }

    public function test_get_roles_by_guard(): void
    {
        Role::factory()->count(2)->create(['guard_name' => 'web']);
        Role::factory()->count(3)->create(['guard_name' => 'api']);

        $webRoles = $this->repository->getRolesByGuard('web');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $webRoles);
        $this->assertEquals(2, $webRoles->count());
    }

    public function test_search_roles(): void
    {
        Role::factory()->create(['name' => 'admin-role']);
        Role::factory()->create(['name' => 'user-role']);
        Role::factory()->create(['name' => 'guest-role']);

        $searchResults = $this->repository->searchRoles('admin');

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $searchResults);
        $this->assertEquals(1, $searchResults->count());
        $this->assertEquals('admin-role', $searchResults->first()->name);
    }

    public function test_get_role_statistics(): void
    {
        Role::factory()->count(2)->create(['guard_name' => 'web']);
        Role::factory()->count(3)->create(['guard_name' => 'api']);

        $statistics = $this->repository->getRoleStatistics();

        $this->assertIsArray($statistics);
        $this->assertEquals(5, $statistics['total_roles']);
        $this->assertEquals(2, $statistics['by_guard']['web']);
        $this->assertEquals(3, $statistics['by_guard']['api']);
    }
}
