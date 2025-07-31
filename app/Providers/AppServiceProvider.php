<?php

namespace App\Providers;

use App\Interfaces\CompanyRepositoryInterface;
use App\Interfaces\ContactRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\profileRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CompanyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\LeadRepository;
use App\Repositories\profileRepository;
use App\Repositories\UserRepository;
use App\Repositories\TenantRepository;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Repositories\ModuleRepository;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Repositories\PermissionRepository;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\PlanRepository;
use App\Repositories\Interfaces\PlanRepositoryInterface;
use App\Services\TenantService;
use App\Services\Interfaces\TenantServiceInterface;
use App\Services\ModuleService;
use App\Services\Interfaces\ModuleServiceInterface;
use App\Services\PermissionService;
use App\Services\Interfaces\PermissionServiceInterface;
use App\Services\RoleService;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\PlanService;
use App\Services\Interfaces\PlanServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(profileRepositoryInterface::class, profileRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(TenantServiceInterface::class, TenantService::class);
        $this->app->bind(ModuleServiceInterface::class, ModuleService::class);
        $this->app->bind(PermissionServiceInterface::class, PermissionService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(PlanServiceInterface::class, PlanService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
