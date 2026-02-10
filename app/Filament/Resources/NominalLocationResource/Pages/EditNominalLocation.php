<?php

namespace App\Filament\Resources\NominalLocationResource\Pages;

use App\Filament\Resources\NominalLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNominalLocation extends EditRecord
{
    protected static string $resource = NominalLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
