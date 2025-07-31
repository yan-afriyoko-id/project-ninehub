<?php

namespace App\Providers;

use App\Repositories\TenantRepository;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Repositories\ModuleRepository;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Repositories\PermissionRepository;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Services\TenantService;
use App\Services\Interfaces\TenantServiceInterface;
use App\Services\ModuleService;
use App\Services\Interfaces\ModuleServiceInterface;
use App\Services\PermissionService;
use App\Services\Interfaces\PermissionServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(TenantServiceInterface::class, TenantService::class);
        $this->app->bind(ModuleServiceInterface::class, ModuleService::class);
        $this->app->bind(PermissionServiceInterface::class, PermissionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
