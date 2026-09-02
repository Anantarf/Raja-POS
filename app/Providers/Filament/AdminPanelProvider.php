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
            ->brandName('RAJA POS')
            ->font('Poppins')
            ->darkMode(false)
            ->colors([
                'primary' => [
                    50 => '239, 246, 255',
                    100 => '219, 234, 254',
                    200 => '191, 219, 254',
                    300 => '147, 197, 253',
                    400 => '96, 165, 250',
                    500 => '59, 130, 246',
                    600 => '37, 99, 235',   // #2563EB (Cashier Royal Blue)
                    700 => '29, 78, 216',
                    800 => '30, 64, 175',
                    900 => '30, 58, 138',
                    950 => '23, 37, 84',
                ],
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
                    /* Custom Filament Theme Matching Cashier UI Style Exactly */
                    body, .fi-body {
                        background-color: #F8FAFC !important;
                        font-family: "Poppins", sans-serif !important;
                    }
                    .fi-topbar {
                        background-color: #0F172A !important;
                        border-bottom: 1px solid #1E293B !important;
                    }
                    .fi-topbar-brand-name {
                        background-color: #F59E0B !important;
                        color: #0F172A !important;
                        font-weight: 800 !important;
                        padding: 4px 12px !important;
                        border-radius: 9999px !important;
                        font-size: 0.75rem !important;
                        letter-spacing: 0.05em !important;
                        text-transform: uppercase !important;
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
                        border-radius: 0.75rem !important;
                    }
                    .fi-section, .fi-card, .fi-wi-stats-overview-stat {
                        background-color: #FFFFFF !important;
                        border: 1px solid #E2E8F0 !important;
                        border-radius: 1rem !important;
                    }
                    .fi-btn-primary {
                        background-color: #2563EB !important;
                        border-color: #2563EB !important;
                        border-radius: 0.75rem !important;
                    }
                    .fi-btn-primary:hover {
                        background-color: #1D4ED8 !important;
                        border-color: #1D4ED8 !important;
                    }
                    .fi-badge-primary {
                        background-color: #EFF6FF !important;
                        color: #2563EB !important;
                        border: 1px solid #BFDBFE !important;
                    }
                </style>'
            )
            ->renderHook(
                'panels::user-menu.before',
                fn (): string => '<a href="/pos" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs flex items-center gap-1.5 shadow-sm transition mr-3"><span>Layar Kasir POS</span> &rarr;</a>'
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
