<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\PanelSwitch\PanelSwitch;

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
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->simple()
                ->labels([
                    'admin' => 'Administración',
                    'attendance' => 'Asistencia',
                ])
                ->icons([
                    'admin' => 'heroicon-o-square-2-stack',
                    'attendance' => 'heroicon-o-clock',
                ]);
        });
    }
}
