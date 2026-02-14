<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public static function getNavigationLabel(): string
    {
        return 'Panel de Administración';
    }

    public function getTitle(): string
    {
        return 'Panel de Administración';
    }
}