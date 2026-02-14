<?php

namespace App\Filament\Pages;

use Promethys\FilamentRevive\Pages\RecycleBin as BaseRecycleBin;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

use Promethys\FilamentRevive\Models\RecycleBinItem;

class RecycleBin extends BaseRecycleBin
{
    use HasPageShield;

    public static function getNavigationBadge(): ?string
    {
        return (string) RecycleBinItem::count();
    }
}
