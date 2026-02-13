<?php

namespace App\Filament\Resources;


use App\Filament\Resources\PersonalResource\Pages;
use App\Filament\Resources\PersonalResource\RelationManagers;
use App\Models\NominalLocation;
use App\Models\Personal;
use App\Models\Position;
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
use Filament\Forms\Components\Select;

class PersonalResource extends Resource
{
    protected static ?string $model = Personal::class;

    protected static ?string $modelPosition = Position::class;

    protected static ?string $modelNominalLocation = NominalLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Personal';

    protected static ?string $pluralLabel = 'Personal';

    protected static ?string $modelLabel = 'Personal';
    protected static ?string $navigationGroup = 'Gestión de Personal';


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
                            ->mask('999999999')
                            ->placeholder('Ej: 123456789')
                            ->maxLength(9)
                            ->minLength(7)
                            ->regex('/^[0-9]+$/')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'maxLength' => 'La cédula debe tener máximo 9 dígitos.',
                                'minLength' => 'La cédula debe tener mínimo 7 dígitos.',
                                'regex' => 'La cédula solo puede contener números.',
                                'unique' => 'Esta cédula ya está registrada.',
                            ]),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombres')
                            ->required()
                            ->maxLength(20) // Límite de 20 caracteres
                            ->regex('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/') // Solo letras y espacios
                            ->validationMessages([
                                'regex' => 'El nombre solo puede contener letras.',
                                'max' => 'El nombre no puede exceder los 20 caracteres.',
                            ]),

                        Forms\Components\TextInput::make('last_name')
                            ->label('Apellidos')
                            ->required()
                            ->maxLength(20) // Límite de 20 caracteres
                            ->regex('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/') // Solo letras y espacios
                            ->validationMessages([
                                'regex' => 'El apellido solo puede contener letras.',
                                'max' => 'El apellido no puede exceder los 20 caracteres.',
                            ]),
                    ])->columns(3),

                Section::make('Contacto y Cargo')
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')
                            ->label('Teléfono')
                            ->tel()
                            ->mask('0000-0000000')
                            ->placeholder('0412-1234567')
                            ->required()
                            // Nota: La máscara ya controla la longitud visualmente
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(50) // Límite solicitado de 50 caracteres
                            ->validationMessages([
                                'email' => 'El formato del correo no es válido.',
                                'max' => 'El correo no puede exceder los 50 caracteres.',
                            ]),

                        Forms\Components\Select::make('id_nominal_location')
                            ->label('Ubicación Nominal')
                            ->options(NominalLocation::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('id_position')
                            ->label('Cargo Actual')
                            ->options(Position::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                Section::make('Archivos y Documentación')
                    ->description('Suba la fotografía')
                    ->schema([
                        FileUpload::make('photo_dir')
                            ->label('Foto de Perfil')
                            ->image()
                            ->directory('fotos-personal')
                            ->required(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_dir')
                    ->searchable()
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('document')
                    ->numeric()
                    ->label('Cédula')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellidos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('nominalLocation.name')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('position.name')
                //     ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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

                InfoSection::make('Detalles de Contacto y Cargo')
                    ->schema([
                        TextEntry::make('phone_number')
                            ->label('Teléfono'),
                        TextEntry::make('email')
                            ->label('Correo Electrónico'),
                        TextEntry::make('nominalLocation.name')
                            ->label('Ubicación Nominal'),
                        TextEntry::make('position.name')
                            ->label('Cargo Actual'),
                    ])->columns(2),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
