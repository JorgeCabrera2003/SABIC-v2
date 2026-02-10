<?php

namespace App\Filament\Resources\NominalLocationResource\Pages;

use App\Filament\Resources\NominalLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNominalLocations extends ListRecords
{
    protected static string $resource = NominalLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
