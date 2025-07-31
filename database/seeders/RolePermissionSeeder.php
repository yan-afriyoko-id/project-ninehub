<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Module;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $user = Role::create(['name' => 'user', 'guard_name' => 'web']);
        $guest = Role::create(['name' => 'guest', 'guard_name' => 'web']);

        // Create permissions based on modules
        $modules = Module::all();

        foreach ($modules as $module) {
            $permissions = $module->getPermissionsToCreate();

            foreach ($permissions as $permissionName) {
                Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Assign permissions to roles
        $allPermissions = Permission::all();

        // Super Admin gets all permissions
        $superAdmin->givePermissionTo($allPermissions);

        // Admin gets most permissions except super admin specific ones
        $adminPermissions = Permission::whereNotIn('name', [
            'super-admin.*'
        ])->get();
        $admin->givePermissionTo($adminPermissions);

        // Manager gets view and edit permissions
        $managerPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'user-management.view',
            'tenant-management.view',
            'plan-management.view',
            'settings.view',
            'user-management.edit',
            'tenant-management.edit',
            'plan-management.edit',
            'settings.edit',
        ])->get();
        $manager->givePermissionTo($managerPermissions);

        // User gets basic view permissions
        $userPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'user-management.view',
            'tenant-management.view',
            'plan-management.view',
        ])->get();
        $user->givePermissionTo($userPermissions);

        // Guest gets only public module permissions
        $guestPermissions = Permission::whereIn('name', [
            'dashboard.view',
        ])->get();
        $guest->givePermissionTo($guestPermissions);
    }
}
