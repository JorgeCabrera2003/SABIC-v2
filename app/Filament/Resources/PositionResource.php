<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PositionResource\Pages;
use App\Filament\Resources\PositionResource\RelationManagers;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Gestión de Personal';

    // Cambia el nombre en el menú lateral
    protected static ?string $navigationLabel = 'Cargos';

    // Cambia el título en la lista (plural)
    protected static ?string $pluralLabel = 'Cargos';

    // Cambia el título en el formulario de creación/edición (singular)
    protected static ?string $modelLabel = 'Cargo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Cargo')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Cargo')
                            ->required()
                            ->unique(ignoreRecord: true) // No permite dos cargos con el mismo nombre
                            ->maxLength(100)
                            // Regex: Permite letras, espacios y caracteres especiales comunes en cargos (/, -, .)
                            ->regex('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\/\-\.]+$/')
                            ->placeholder('Ej: Analista de Sistemas / Director - RRHH')
                            ->validationMessages([
                                'unique' => 'Este nombre de cargo ya existe en el sistema.',
                                'regex' => 'El nombre del cargo solo puede contener letras, espacios y (/, -, .)',
                                'max' => 'El cargo no debe exceder los 100 caracteres.',
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Cargo')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}
