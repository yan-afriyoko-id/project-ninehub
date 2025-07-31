<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Module;

class TenantModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();
        $modules = Module::all();

        foreach ($tenants as $tenant) {
            // Assign modules based on plan
            $planFeatures = $tenant->plan->features ?? [];

            foreach ($modules as $module) {
                // Check if module is available for this plan
                if (in_array($module->slug, $planFeatures) || $module->is_public) {
                    $tenant->assignModule($module->id, null, null);
                }
            }
        }

        $this->command->info('Tenant modules assigned successfully!');
    }
}
