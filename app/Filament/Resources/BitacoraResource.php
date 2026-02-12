<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraResource\Pages;
use App\Filament\Resources\BitacoraResource\RelationManagers;
use App\Models\Bitacora;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class BitacoraResource extends Resource
{
    protected static ?string $model = Bitacora::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    // Cambia el nombre en el menú lateral
    protected static ?string $navigationLabel = 'Bitacora';

    // Cambia el título en la lista (singular)
    protected static ?string $pluralLabel = 'Bitacora';

    // Cambia el título en el formulario de creación/edición (singular)
    protected static ?string $modelLabel = 'Bitacora';

    protected static ?string $navigationGroup = 'Administración del Sistema';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('accion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tabla_afectada')
                    ->maxLength(255),
                Forms\Components\TextInput::make('registro_id')
                    ->numeric(),
                Forms\Components\TextInput::make('valores_anteriores'),
                Forms\Components\TextInput::make('valores_nuevos'),
                Forms\Components\TextInput::make('ip_address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_agent')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bd_user')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('accion')
                    ->label('Acción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tabla_afectada')
                    ->label('Tabla Afectada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registro_id')
                    ->label('ID del Registro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Usuario')
                    ->searchable(['name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha y Hora')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            ])->defaultSort('created_at', 'desc')->recordUrl(null);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Detalles')
                    ->schema([
                        TextEntry::make('user_agent')
                            ->label('Agente'),
                        TextEntry::make('ip_address')
                            ->label('Dirección IP'),
                        TextEntry::make('bd_user')
                            ->label('Usuario BD'),
                        TextEntry::make('user_id')
                            ->label('ID del Usuario')
                            ->formatStateUsing(function ($record) {
                                return "{$record->usuario->id}# {$record->usuario->name}";
                            }),
                    ])->columns(2),
                InfoSection::make('Acción')
                    ->schema([
                        TextEntry::make('accion')
                            ->label('Acción'),
                        TextEntry::make('tabla_afectada')
                            ->label('Tabla Afectada'),
                        TextEntry::make('registro_id')
                            ->label('ID del Registro'),
                        TextEntry::make('valores_anteriores')
                            ->label('Valores Anteriores'),
                        TextEntry::make('valores_nuevos')
                            ->label('Valores Nuevos'),
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
            'index' => Pages\ListBitacoras::route('/'),
            // 'create' => Pages\CreateBitacora::route('/create'),
            // 'edit' => Pages\EditBitacora::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
