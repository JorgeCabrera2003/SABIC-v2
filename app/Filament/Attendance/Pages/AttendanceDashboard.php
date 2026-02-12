<?php

namespace App\Filament\Attendance\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class AttendanceDashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = '/';
    protected static ?string $navigationLabel = 'Inicio';
    protected static ?string $title = 'Panel de Asistencia';

    public function getColumns(): int|string|array
    {
        return 1;
    }
}