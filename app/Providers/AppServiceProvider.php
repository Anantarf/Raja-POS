<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('OWNER') || $user->hasRole('SUPERADMIN') || $user->hasRole('ADMIN')) {
                // Owner and Admin have full access to management
                if ($user->hasRole('OWNER')) {
                    return true;
                }
            }
            return $user->hasPermission($ability) ? true : null;
        });
    }
}
