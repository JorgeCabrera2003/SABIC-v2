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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AttendancePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('attendance')
            ->path('attendance') // Accesible en /attendance
            ->brandName('Registro de Asistencia')
            ->favicon(asset('favicon.png'))
            ->colors([
                'primary' => Color::Yellow,
            ])
            ->discoverResources(in: app_path('Filament/Attendance/Resources'), for: 'App\\Filament\\Attendance\\Resources')
            ->discoverPages(in: app_path('Filament/Attendance/Pages'), for: 'App\\Filament\\Attendance\\Pages')
            ->pages([
                \App\Filament\Attendance\Pages\AttendanceDashboard::class,
            ])
            ->homeUrl(fn(): string => url('/attendance/register-attendance'))
            ->discoverWidgets(in: app_path('Filament/Attendance/Widgets'), for: 'App\\Filament\\Attendance\\Widgets')
            ->widgets([
                // Widgets personalizados para la asistencia
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
                    // Comentar o eliminar esta línea para hacerlo público
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])
            ->topNavigation() // Navegación superior para mejor UX
            ->sidebarCollapsibleOnDesktop(false); // Sidebar siempre visible
    }
}