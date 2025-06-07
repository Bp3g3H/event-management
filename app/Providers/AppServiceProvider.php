<?php

namespace App\Providers;

use App\Models\Attendee;
use App\Observers\AttendeeObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Attendee::observe(AttendeeObserver::class);
    }
}
