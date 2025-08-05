<?php

namespace App\Providers;

use App\Interfaces\CompanyRepositoryInterface;
use App\Interfaces\ContactRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\ProfileRepositoryInterface;
use App\Interfaces\TenantRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CompanyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\LeadRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\TenantRepository;
use App\Repositories\UserRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\PlanRepository;
use App\Repositories\Interfaces\PlanRepositoryInterface;
use App\Services\TenantService;
use App\Services\Interfaces\TenantServiceInterface;
use App\Services\PermissionService;
use App\Services\Interfaces\PermissionServiceInterface;
use App\Services\RoleService;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\PlanService;
use App\Services\Interfaces\PlanServiceInterface;
use App\Services\UserService;
use App\Services\LeadService;
use App\Services\ContactService;
use App\Services\CompanyService;
use App\Services\TenantSettingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);

        // Bind additional services
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);

        $this->app->bind(TenantServiceInterface::class, TenantService::class);
        $this->app->bind(PermissionServiceInterface::class, PermissionService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(PlanServiceInterface::class, PlanService::class);

        // Bind concrete services
        $this->app->bind(UserService::class, UserService::class);
        $this->app->bind(LeadService::class, LeadService::class);
        $this->app->bind(\App\Services\Interfaces\LeadServiceInterface::class, LeadService::class);
        $this->app->bind(ContactService::class, ContactService::class);
        $this->app->bind(\App\Services\Interfaces\ContactServiceInterface::class, ContactService::class);
        $this->app->bind(CompanyService::class, CompanyService::class);
        $this->app->bind(\App\Services\Interfaces\CompanyServiceInterface::class, CompanyService::class);
        $this->app->bind(TenantSettingService::class, TenantSettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
