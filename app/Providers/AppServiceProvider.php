<?php

namespace App\Providers;

use App\Repositories\TenantRepository;
use App\Repositories\TenantRepositoryInterface;
use App\Services\TenantService;
use App\Services\TenantServiceInterface;
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

        // Service bindings
        $this->app->bind(TenantServiceInterface::class, TenantService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
