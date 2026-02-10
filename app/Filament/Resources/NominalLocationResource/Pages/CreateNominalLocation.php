<?php

namespace App\Filament\Resources\NominalLocationResource\Pages;

use App\Filament\Resources\NominalLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNominalLocation extends CreateRecord
{
    protected static string $resource = NominalLocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
