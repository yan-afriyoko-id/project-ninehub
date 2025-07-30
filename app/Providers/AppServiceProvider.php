<?php

namespace App\Providers;

use App\Interfaces\profileRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
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

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
