<?php

namespace App\Providers;

use App\Repositories\TenantRepository;
use App\Repositories\TenantRepositoryInterface;
use App\Repositories\ModuleRepository;
use App\Repositories\ModuleRepositoryInterface;
use App\Services\TenantService;
use App\Services\TenantServiceInterface;
use App\Services\ModuleService;
use App\Services\ModuleServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);

        // Service bindings
        $this->app->bind(TenantServiceInterface::class, TenantService::class);
        $this->app->bind(ModuleServiceInterface::class, ModuleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
