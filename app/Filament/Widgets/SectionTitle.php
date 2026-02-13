<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SectionTitle extends Widget
{
    protected static string $view = 'filament.widgets.section-title';

    protected int | string | array $columnSpan = 'full';

    // Esto define la posición. Ponlo antes de tus widgets de usuarios.
    protected static ?int $sort = 3; 
}