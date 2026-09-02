<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('Raja Aksesoris POS')
            ->font('Poppins')
            ->darkMode(false)
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'success' => Color::Emerald,
                'info' => Color::Sky,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->renderHook(
                'panels::head.done',
                fn (): string => '<style>
                    /* Custom Filament Light Theme Matching Cashier UI Style */
                    body, .fi-body {
                        background-color: #F8FAFC !important;
                    }
                    .fi-topbar {
                        background-color: #0F172A !important;
                        border-bottom: 1px solid #1E293B !important;
                    }
                    .fi-topbar-brand-name {
                        color: #FFFFFF !important;
                        font-weight: 700 !important;
                        letter-spacing: -0.025em;
                    }
                    .fi-topbar-item, .fi-topbar-user-menu button {
                        color: #E2E8F0 !important;
                    }
                    .fi-sidebar {
                        background-color: #FFFFFF !important;
                        border-right: 1px solid #E2E8F0 !important;
                    }
                    .fi-sidebar-header {
                        background-color: #0F172A !important;
                        border-bottom: 1px solid #1E293B !important;
                    }
                    .fi-sidebar-header .fi-logo {
                        color: #FFFFFF !important;
                    }
                    .fi-sidebar-group-label {
                        font-weight: 700 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        color: #64748B !important;
                        font-size: 0.7rem !important;
                    }
                    .fi-sidebar-item-active > a {
                        background-color: #2563EB !important;
                        color: #FFFFFF !important;
                        font-weight: 600 !important;
                    }
                    .fi-section, .fi-card, .fi-wi-stats-overview-stat {
                        background-color: #FFFFFF !important;
                        border: 1px solid #E2E8F0 !important;
                        border-radius: 1rem !important;
                    }
                    .fi-btn-primary {
                        background-color: #2563EB !important;
                    }
                    .fi-btn-primary:hover {
                        background-color: #1D4ED8 !important;
                    }
                </style>'
            )
            ->renderHook(
                'panels::user-menu.before',
                fn (): string => '<a href="/pos" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 shadow-sm transition mr-2"><span>Layar Kasir POS</span> &rarr;</a>'
            )
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
