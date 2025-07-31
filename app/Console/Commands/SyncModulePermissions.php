<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Module;
use Spatie\Permission\Models\Permission;

class SyncModulePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:sync-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions based on modules configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting module permissions sync...');

        $modules = Module::all();
        $createdPermissions = 0;
        $existingPermissions = 0;

        foreach ($modules as $module) {
            $this->line("Processing module: {$module->name}");

            $permissions = $module->getPermissionsToCreate();

            foreach ($permissions as $permissionName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);

                if ($permission->wasRecentlyCreated) {
                    $createdPermissions++;
                    $this->line("  ✓ Created permission: {$permissionName}");
                } else {
                    $existingPermissions++;
                    $this->line("  - Permission already exists: {$permissionName}");
                }
            }
        }

        $this->info("Sync completed!");
        $this->info("Created: {$createdPermissions} permissions");
        $this->info("Existing: {$existingPermissions} permissions");

        return 0;
    }
}
