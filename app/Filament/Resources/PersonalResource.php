<?php

namespace App\Filament\Resources;


use App\Filament\Resources\PersonalResource\Pages;
use App\Filament\Resources\PersonalResource\RelationManagers;
use App\Models\Personal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Section; // Importar la clase Section
use Filament\Forms\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class PersonalResource extends Resource
{
    protected static ?string $model = Personal::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Identitaria')
                    ->description('Datos principales del trabajador')
                    ->schema([
                        Forms\Components\TextInput::make('document')
                            ->label('Cédula de Identidad')
                            ->required()
                            ->numeric()
                            ->columnSpan(1), // Ocupa una columna
                        // Grid::make(2) // Una rejilla de 2 columnas dentro de la sección
                        //     ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombres')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->label('Apellidos')
                                    ->required(),
                            // ]),
                    ])->columns(3),

                Section::make('Contacto y Cargo')
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')
                            ->label('Teléfono')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('nominal_location')
                            ->label('Ubicación Nominal')
                            ->required(),
                        Forms\Components\TextInput::make('position')
                            ->label('Cargo Actual')
                            ->required(),
                    ])->columns(2),

                Section::make('Archivos y Documentación')
                    ->description('Suba la fotografía')
                    ->schema([
                        FileUpload::make('photo_dir')
                            ->label('Foto de Perfil')
                            ->image()
                            ->directory('fotos-personal'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_dir')
                    ->searchable()
                    ->circular(),
                Tables\Columns\TextColumn::make('document')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nominal_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Ficha del Empleado')
                    ->schema([
                        ImageEntry::make('photo_dir')
                            ->label('Fotografía')
                            ->circular(),
                        TextEntry::make('document')
                            ->label('Cédula'),
                        TextEntry::make('name')
                            ->label('Nombres'),
                        TextEntry::make('last_name')
                            ->label('Apellidos'),
                    ])->columns(2),

                InfoSection::make('Documentación Adjunta')
                    ->schema([
                        TextEntry::make('curriculo_dir')
                            ->label('Currículo Vitae')
                            ->formatStateUsing(fn() => 'Abrir documento PDF')
                            ->color('primary')
                            ->icon('heroicon-o-document-arrow-down')
                            ->url(fn($record) => asset('storage/' . $record->curriculo_dir), shouldOpenInNewTab: true)
                            ->visible(fn($record) => !empty($record->curriculo_dir)),
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
            'index' => Pages\ListPersonals::route('/'),
            'create' => Pages\CreatePersonal::route('/create'),
            'edit' => Pages\EditPersonal::route('/{record}/edit'),
        ];
    }
}
