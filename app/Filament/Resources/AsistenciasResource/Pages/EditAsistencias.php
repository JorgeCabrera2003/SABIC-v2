<?php

namespace App\Filament\Resources\AsistenciasResource\Pages;

use App\Filament\Resources\AsistenciasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsistencias extends EditRecord
{
    protected static string $resource = AsistenciasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
