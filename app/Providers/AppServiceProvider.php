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
        $this->app->bind('path.public', function () {
            $cpanelSubfolder = base_path('../public_html/raja-pos.my.id');
            if (is_dir($cpanelSubfolder)) {
                return $cpanelSubfolder;
            }

            $cpanelRoot = base_path('../public_html');
            if (is_dir($cpanelRoot)) {
                return $cpanelRoot;
            }

            return base_path('public');
        });
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
