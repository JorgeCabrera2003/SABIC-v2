<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Personal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


class AttendanceStats extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Estadísticas de Asistencias';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        $today = now()->toDateString();
        $todayCount = Attendance::where('day', $today)->count();
        $totalEmployees = Personal::where('status', 'active')->orWhere('status', 'authorized')->count();
        $manualCount = Attendance::where('day', $today)->where('record_type', 'MANUAL')->count();
        $fingerCount = Attendance::where('day', $today)->where('record_type', 'HUELLA')->count();

        return [
            Stat::make('Registros Hoy', $todayCount)
                ->description('Asistencias registradas hoy')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pendientes Hoy', max(0, $totalEmployees - $todayCount))
                ->description('Empleados por registrar hoy')
                ->descriptionIcon('heroicon-m-clock')
                ->color($todayCount >= $totalEmployees ? 'success' : 'warning'),

            Stat::make('Registros Manuales', $manualCount)
                ->description('Registros manuales del dia')
                ->descriptionIcon('heroicon-m-pencil')
                ->color('success'),

            Stat::make('Registros Por Huella', $fingerCount)
                ->description('Registros por huella dactilar')
                ->descriptionIcon('heroicon-m-finger-print')
                ->color('success'),
        ];
    }
}