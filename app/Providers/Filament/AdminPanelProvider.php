<?php

namespace App\Providers\Filament;

use App\Http\Middleware\CheckPanelRole;
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
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Zinc,
                'danger' => Color::Red,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'info' => Color::Blue,
            ])
            ->font('Inter')
            ->brandName('Scotelaro Admin')
            ->brandLogo(asset('logo.png'))
            ->favicon(asset('favicon.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->resources([
                \App\Filament\Resources\StudentResource::class,
                \App\Filament\Resources\StaffResource::class,
                \App\Filament\Resources\EnrollmentResource::class,
                \App\Filament\Resources\ModalityResource::class,
                \App\Filament\Resources\GymClassResource::class,
                \App\Filament\Resources\PricingTierResource::class,
                \App\Filament\Resources\InviteResource::class,
                \App\Filament\Resources\ExpenseResource::class,
                \App\Filament\Resources\ResourceResource::class,
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
                CheckPanelRole::class . ':admin',
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->topNavigation(false)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->breadcrumbs(false)
            ->darkMode(true); // Force dark theme
    }
}
