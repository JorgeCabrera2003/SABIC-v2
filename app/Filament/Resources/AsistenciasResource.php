<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsistenciasResource\Pages;
use App\Filament\Resources\AsistenciasResource\RelationManagers;
use App\Models\Asistencias;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsistenciasResource extends Resource
{
    protected static ?string $model = Asistencias::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_empleado')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('dia')
                    ->required(),
                Forms\Components\TextInput::make('hora')
                    ->required(),
                Forms\Components\TextInput::make('tipo_registro')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('foto_dir')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nota')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_empleado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dia')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora'),
                Tables\Columns\TextColumn::make('tipo_registro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('foto_dir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAsistencias::route('/'),
            'create' => Pages\CreateAsistencias::route('/create'),
            'edit' => Pages\EditAsistencias::route('/{record}/edit'),
        ];
    }
}
