<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NominalLocationResource\Pages;
use App\Filament\Resources\NominalLocationResource\RelationManagers;
use App\Models\NominalLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NominalLocationResource extends Resource
{
    protected static ?string $model = NominalLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Ubicación Física / Nominal')
                ->description('Especifique el nombre de la sede y el nivel correspondiente.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre de la Ubicación')
                        ->required()
                        ->unique(ignoreRecord: true) // Evita nombres duplicados
                        ->maxLength(20  )
                        // Regex: Permite letras, números, espacios, puntos y guiones
                        ->regex('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.\-]+$/')
                        ->placeholder('Ej: Sede Principal o Almacén Central')
                        ->validationMessages([
                            'unique' => 'Esta ubicación ya está registrada.',
                            'regex' => 'El nombre solo puede contener letras, números, espacios, puntos o guiones.',
                            'max' => 'El nombre no debe exceder los 20   caracteres.',
                        ]),

                    Forms\Components\TextInput::make('floor')
                        ->label('Piso o Nivel')
                        ->maxLength(50)
                        ->placeholder('Ej: Planta Baja / Piso 2 / Sótano')
                        // Regex: Alfanumérico simple
                        ->regex('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.]+$/')
                        ->validationMessages([
                            'regex' => 'El formato del piso no es válido.',
                            'max' => 'Este campo no debe exceder los 50 caracteres.',
                        ]),
                ])->columns(2),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('floor')
                    ->label('Piso')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNominalLocations::route('/'),
            'create' => Pages\CreateNominalLocation::route('/create'),
            'edit' => Pages\EditNominalLocation::route('/{record}/edit'),
        ];
    }
}
