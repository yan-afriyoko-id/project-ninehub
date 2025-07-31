<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Dashboard utama aplikasi',
                'icon' => 'dashboard',
                'route' => 'dashboard',
                'order' => 1,
                'is_active' => true,
                'is_public' => true,
                'permissions' => ['view'],
            ],
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'description' => 'Manajemen pengguna sistem',
                'icon' => 'users',
                'route' => 'users.index',
                'order' => 2,
                'is_active' => true,
                'is_public' => false,
                'permissions' => ['view', 'create', 'edit', 'delete'],
            ],
            [
                'name' => 'Tenant Management',
                'slug' => 'tenant-management',
                'description' => 'Manajemen tenant/klien',
                'icon' => 'building',
                'route' => 'tenants.index',
                'order' => 3,
                'is_active' => true,
                'is_public' => false,
                'permissions' => ['view', 'create', 'edit', 'delete'],
            ],
            [
                'name' => 'Plan Management',
                'slug' => 'plan-management',
                'description' => 'Manajemen paket berlangganan',
                'icon' => 'package',
                'route' => 'plans.index',
                'order' => 4,
                'is_active' => true,
                'is_public' => false,
                'permissions' => ['view', 'create', 'edit', 'delete'],
            ],
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'description' => 'Pengaturan sistem',
                'icon' => 'settings',
                'route' => 'settings.index',
                'order' => 5,
                'is_active' => true,
                'is_public' => false,
                'permissions' => ['view', 'edit'],
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['slug' => $module['slug']],
                $module
            );
        }
    }
}
