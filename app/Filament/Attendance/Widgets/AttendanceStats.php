<?php

namespace App\Filament\Attendance\Widgets;

use App\Models\Attendance;
use App\Models\Personal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AttendanceStats extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
        $today = now()->toDateString();

        $todayCount = Attendance::where('day', $today)->count();
        $totalEmployees = Personal::where('status', 'active')->orWhere('status', 'authorized')->count();

        return [
            Stat::make('Registros Hoy', $todayCount)
                ->description('Asistencias registradas hoy')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Empleados', $totalEmployees)
                ->description('Empleados registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Pendientes', max(0, $totalEmployees - $todayCount))
                ->description('Empleados por registrar hoy')
                ->descriptionIcon('heroicon-m-clock')
                ->color($todayCount >= $totalEmployees ? 'success' : 'warning'),
        ];
    }
}