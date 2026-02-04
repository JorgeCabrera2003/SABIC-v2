<?php

namespace App\Filament\Resources\AsistenciasResource\Pages;

use App\Filament\Resources\AsistenciasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsistencias extends ListRecords
{
    protected static string $resource = AsistenciasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
