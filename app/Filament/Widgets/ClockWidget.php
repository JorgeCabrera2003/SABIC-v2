<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class ClockWidget extends Widget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Hora / Fecha';

    public function getHeading(): ?string
    {
        return static::$heading;
    }

    protected static string $view = 'filament.widgets.clock-widget';

    protected static ?int $sort = 0; // Aseguramos que esté en la parte superior

    protected int|string|array $columnSpan = 'full'; // Ocupar todo el ancho
}
