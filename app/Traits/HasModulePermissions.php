<?php

namespace App\Traits;

use App\Models\Module;

trait HasModulePermissions
{
    /**
     * Check if user can access a specific module.
     */
    public function canAccessModule($moduleSlug): bool
    {
        $module = Module::where('slug', $moduleSlug)->first();

        if (!$module) {
            return false;
        }

        // If module is public, allow access
        if ($module->is_public) {
            return true;
        }

        // Check if user has any permission for this module
        $modulePermissions = $module->getPermissionsToCreate();
        foreach ($modulePermissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can perform specific action on module.
     */
    public function canPerformAction($moduleSlug, $action): bool
    {
        $permission = $moduleSlug . '.' . $action;
        return $this->hasPermissionTo($permission);
    }

    /**
     * Get accessible modules for user.
     */
    public function getAccessibleModules()
    {
        return Module::active()->ordered()->get()->filter(function ($module) {
            return $this->canAccessModule($module->slug);
        });
    }

    /**
     * Get user's permissions grouped by module.
     */
    public function getPermissionsByModule(): array
    {
        $modules = Module::all();
        $permissionsByModule = [];

        foreach ($modules as $module) {
            $modulePermissions = [];
            $modulePermissionNames = $module->getPermissionsToCreate();

            foreach ($modulePermissionNames as $permissionName) {
                if ($this->hasPermissionTo($permissionName)) {
                    $action = str_replace($module->slug . '.', '', $permissionName);
                    $modulePermissions[] = $action;
                }
            }

            if (!empty($modulePermissions)) {
                $permissionsByModule[$module->slug] = [
                    'module' => $module,
                    'permissions' => $modulePermissions
                ];
            }
        }

        return $permissionsByModule;
    }
}
