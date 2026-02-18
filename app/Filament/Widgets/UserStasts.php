<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Personal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


class UserStasts extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Estadísticas de Usuarios';

    protected static ?int $sort = 4;

    protected function getStats(): array
    {

        $today = now()->toDateString();
        $todayCount = Attendance::where('day', $today)->count();
        $totalEmployees = Personal::where('status', 'active')->orWhere('status', 'authorized')->count();

        return [
            Stat::make('Usuarios Totales', \App\Models\User::count())
                ->description('Registrados en la plataforma')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}