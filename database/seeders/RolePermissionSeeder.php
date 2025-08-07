<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $guest = Role::firstOrCreate(['name' => 'guest', 'guard_name' => 'web']);

        // Define permissions manually since we're not using modules table anymore
        $permissions = [
            // Dashboard
            'dashboard.view',

            // User Management
            'user-management.view',
            'user-management.create',
            'user-management.edit',
            'user-management.delete',

            // Tenant Management
            'tenant-management.view',
            'tenant-management.create',
            'tenant-management.edit',
            'tenant-management.delete',
            'tenant-management.activate',
            'tenant-management.suspend',

            // Plan Management
            'plan-management.view',
            'plan-management.create',
            'plan-management.edit',
            'plan-management.delete',

            // Settings
            'settings.view',
            'settings.edit',

            // Company Management
            'company.view',
            'company.create',
            'company.edit',
            'company.delete',

            // Contact Management
            'contact.view',
            'contact.create',
            'contact.edit',
            'contact.delete',

            // Lead Management
            'lead.view',
            'lead.create',
            'lead.edit',
            'lead.delete',

            // Profile Management
            'profile.view',
            'profile.create',
            'profile.edit',
            'profile.delete',

            // Chat/AI
            'chat.send',
            'chat.history',
            'chat.clear',
        ];

        // Create permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
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
            'company.view',
            'contact.view',
            'lead.view',
            'profile.view',
            'user-management.edit',
            'tenant-management.edit',
            'plan-management.edit',
            'settings.edit',
            'company.edit',
            'contact.edit',
            'lead.edit',
            'profile.edit',
        ])->get();
        $manager->givePermissionTo($managerPermissions);

        // User gets basic view permissions
        $userPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'company.view',
            'contact.view',
            'lead.view',
            'profile.view',
            'chat.send',
            'chat.history',
        ])->get();
        $user->givePermissionTo($userPermissions);

        // Guest gets only public module permissions
        $guestPermissions = Permission::whereIn('name', [
            'dashboard.view',
        ])->get();
        $guest->givePermissionTo($guestPermissions);
    }
}
