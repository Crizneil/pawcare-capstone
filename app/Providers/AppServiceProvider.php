<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        // Fix for "mail::" components not resolving in some Laravel environments
        \Illuminate\Support\Facades\Blade::anonymousComponentPath(
            base_path('vendor/laravel/framework/src/Illuminate/Mail/resources/views/html'),
            'mail'
        );
    }
}
