<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // The PDF facade is registered by the package's service provider
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share Auth facade with all views
        View::share('Auth', Auth::class);

        // Add Blade directives for Auth
        \Illuminate\Support\Facades\Blade::if('admin', function () {
            return Auth::check() && Auth::user()->isAdmin();
        });

        \Illuminate\Support\Facades\Blade::if('auth', function () {
            return Auth::check();
        });
    }
}
