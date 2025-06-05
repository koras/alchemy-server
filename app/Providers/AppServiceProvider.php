<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\YoomoneyServiceInterface;
use App\Services\YoomoneyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind( YoomoneyServiceInterface::class, YoomoneyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
