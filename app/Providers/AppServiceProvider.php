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
        $cpanelSubfolder = base_path('../public_html/raja-pos.my.id');
        if (is_dir($cpanelSubfolder)) {
            $path = realpath($cpanelSubfolder) ?: $cpanelSubfolder;
            $this->app->instance('path.public', $path);
            if (method_exists($this->app, 'usePublicPath')) {
                $this->app->usePublicPath($path);
            }
        } elseif (is_dir(base_path('../public_html'))) {
            $cpanelRoot = base_path('../public_html');
            $path = realpath($cpanelRoot) ?: $cpanelRoot;
            $this->app->instance('path.public', $path);
            if (method_exists($this->app, 'usePublicPath')) {
                $this->app->usePublicPath($path);
            }
        }
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
