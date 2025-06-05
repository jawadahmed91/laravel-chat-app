<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\IncomingEntry;

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
        // Enable Telescope to track everything (optional — can be customized)
        Telescope::filter(function (IncomingEntry $entry) {
            return true;
        });
    }
}
