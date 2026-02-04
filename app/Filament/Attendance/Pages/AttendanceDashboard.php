<?php

namespace App\Filament\Attendance\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class AttendanceDashboard extends BaseDashboard
{
    protected static string $routePath = '/';
    protected static ?string $navigationLabel = 'Inicio';
    protected static ?string $title = 'Panel de Asistencia';
    
    public function getColumns(): int|string|array
    {
        return 1;
    }
}