<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Personal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

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
                Tables\Columns\TextColumn::make('personal.name')
                    ->label('Empleado')
                    ->formatStateUsing(function ($record) {
                        return "{$record->personal->name} {$record->personal->last_name}";
                    })
                    ->searchable(['name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('day')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hour')
                    ->sortable(),
                Tables\Columns\TextColumn::make('record_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('observation')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])->recordUrl(null);;
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
            'index' => Pages\ListAttendance::route('/'),
            // 'create' => Pages\CreateAttendance::route('/create'),
            // 'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
