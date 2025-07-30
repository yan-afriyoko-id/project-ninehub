<?php

namespace App\Providers;

use App\Interfaces\CompanyRepositoryInterface;
use App\Interfaces\ContactRepositoryInterface;
use App\Interfaces\profileRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CompanyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\profileRepository;
use App\Repositories\UserRepository;
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

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
