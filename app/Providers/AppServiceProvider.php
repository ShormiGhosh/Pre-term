<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS and set root URL for devtunnels
        if (str_contains(config('app.url'), 'devtunnels.ms')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
    }
}
