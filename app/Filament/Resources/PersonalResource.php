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

class PersonalResource extends Resource
{
    protected static ?string $model = Personal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Identitaria')
                    ->description('Datos principales del trabajador')
                    ->schema([
                        Forms\Components\TextInput::make('cedula')
                            ->label('Cédula de Identidad')
                            ->required()
                            ->numeric()
                            ->columnSpan(1), // Ocupa una columna
                        Grid::make(2) // Una rejilla de 2 columnas dentro de la sección
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombres')
                                    ->required(),
                                Forms\Components\TextInput::make('apellido')
                                    ->label('Apellidos')
                                    ->required(),
                            ]),
                    ])->columns(2),

                Section::make('Contacto y Cargo')
                    ->schema([
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('ubicacion_nominal')
                            ->label('Ubicación Nominal')
                            ->required(),
                        Forms\Components\TextInput::make('cargo')
                            ->label('Cargo Actual')
                            ->required(),
                    ])->columns(2),

                Section::make('Archivos y Documentación')
                    ->description('Suba la fotografía y el currículo en formato PDF')
                    ->schema([
                        FileUpload::make('foto_dir')
                            ->label('Foto de Perfil')
                            ->image()
                            ->directory('fotos-personal'),
                        FileUpload::make('curriculo_dir')
                            ->label('Currículo Vitae (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('curriculos'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_dir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cedula')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ubicacion_nominal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cargo')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('curriculo_dir')
                //     ->searchable(),
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
