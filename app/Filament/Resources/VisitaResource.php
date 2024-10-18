<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaResource\Pages;
use App\Filament\Resources\VisitaResource\RelationManagers;
use App\Models\Visita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitaResource extends Resource
{
    protected static ?string $model = Visita::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('medico_id')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('visitador_id')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('latitud')
                ->numeric()
                ->extraAttributes(['id' => 'latitude']),
            Forms\Components\TextInput::make('longitude')
                ->numeric()
                ->extraAttributes(['id' => 'longitude']),
            Forms\Components\TextInput::make('producto')
                ->maxLength(255)
                ->default(null),
            Forms\Components\TextInput::make('observacion')
                ->maxLength(255)
                ->default(null),
            Forms\Components\TextInput::make('estado')
                ->required()
                ->maxLength(255),
    
            Actions::make([
                Action::make('verificarUbicacion')
                    ->label('Verificar ubicación')
                    ->color('primary')
                    ->extraAttributes([
                        'id' => 'verificar-ubicacion',  // Solo el ID, sin 'onclick'
                    ]),
            ]),
        ]);
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medico_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('visitador_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitud')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('observacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
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
            'index' => Pages\ListVisitas::route('/'),
            'create' => Pages\CreateVisita::route('/create'),
            'edit' => Pages\EditVisita::route('/{record}/edit'),
        ];
    }
}
