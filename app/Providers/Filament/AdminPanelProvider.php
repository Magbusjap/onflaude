<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->path(option('admin_path', 'admin'))
            ->login()
            ->brandLogo(asset('themes/default/onflaude-logo.png'))
            ->brandLogoHeight('36px')
            ->favicon(
                option('site_favicon') 
                    ? asset('storage/' . option('site_favicon'))
                    : asset('themes/default/onflaude-favicon.svg')
            )
            ->renderHook(
                'panels::page.start',
                fn (): string => '',
            )
            ->colors([
                'primary' => Color::hex('#003893'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->renderHook('panels::sidebar.footer', fn (): string => '
                <div class="of-sidebar-toggle">
                    <button onclick="ofToggleSidebar()" title="Collapse menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M3 6h18M3 18h18"/>
                        </svg>
                        <span class="of-sidebar-toggle-label">Collapse menu</span>
                    </button>
                </div>
            ')
            ->renderHook('panels::sidebar.nav.start', fn (): string => '
                <img
                    src="' . asset('themes/default/onflaude-favicon.svg') . '"
                    class="fi-brand-favicon"
                    style="width: 2.5rem; height: 2.5rem; margin: 0.5rem auto;"
                    alt="OnFlaude"
                >
            ')
            ->renderHook('panels::head.end', fn (): string =>
                '<link rel="stylesheet" href="' . asset('css/filament/index.css') . '?v=' . 
                filemtime(public_path('css/filament/index.css')) . '">' .
                '<link rel="stylesheet" href="' . asset('css/filament/onflaude.css') . '">'
            )
            ->renderHook('panels::body.end', fn (): string =>
                '<script src="' . asset('js/filament/onflaude.js') . '?v=' . filemtime(public_path('js/filament/onflaude.js')) . '"></script>'
            )
            ->renderHook('panels::topbar.start', fn (): string => 
                view('filament.topbar.left')->render()
            )
            ->globalSearch(true)
            ->globalSearchKeyBindings(['ctrl+k', 'cmd+k'])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}