<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\YoomoneyServiceInterface;
use App\Contracts\Services\FeedbackServiceInterface;
use App\Contracts\Services\EventServiceInterface;
use App\Services\YoomoneyService;
use App\Models\Feedback;
use App\Models\AppEvent;



use App\Services\FeedbackService;
use App\Services\EventService;

use App\Contracts\Models\FeedbackInterface;
use App\Contracts\Models\AppEventInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind( AppEventInterface::class, AppEvent::class);
        $this->app->bind( EventServiceInterface::class, EventService::class);

        $this->app->bind( FeedbackServiceInterface::class, FeedbackService::class);
        $this->app->bind( FeedbackInterface::class, Feedback::class);
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
