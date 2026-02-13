<?php

namespace App\Filament\Widgets;

use App\Models\Personal;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {

        $today = now()->toDateString();
        $activeEmployees = Personal::where('status', 'active')->count();
        $inactiveEmployees = Personal::where('status', 'inactive')->count();
        $vacationEmployees = Personal::where('status', 'vacation')->count();
        $authorizedEmployees = Personal::where('status', 'authorized')->count();
        $unauthorizedEmployees = Personal::where('status', 'unauthorized')->count();

        return [
            Stat::make('Personal Total', Personal::count())
                ->description('Registrados en la plataforma')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Empleados Activos', $activeEmployees)
                ->description('Empleados activos')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Empleados Inactivos', $inactiveEmployees)
                ->description('Empleados inactivos')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
                
            Stat::make('Empleados de Vacaciones', $vacationEmployees)
                ->description('Empleados de vacaciones')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
                
            Stat::make('Empleados Autorizados', $authorizedEmployees)
                ->description('Empleados autorizados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
                
            Stat::make('Empleados no Autorizados', $unauthorizedEmployees)
                ->description('Empleados no autorizados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
