<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitadorResource\Pages;
use App\Filament\Resources\VisitadorResource\RelationManagers;
// use Filament\Actions\CreateAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use App\Models\Visitador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;


class VisitadorResource extends Resource
{
    protected static ?string $model = Visitador::class;
    protected static ?string $navigationLabel='Visitadores';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('correo')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->email()
                    ->maxLength(255) ->validationMessages([
                        'unique' => 'Este correo ya existe en el sistema',
                    ]),
                Forms\Components\TextInput::make('celular')
                    ->required()
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\Toggle::make('estado')
                    ->required()->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
       
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('celular')
                    ->searchable(),
                    Tables\Columns\ToggleColumn::make('estado')
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('estado')->label('Estado')
                ->multiple()
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    
                    ]),
                    Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Desde:'),
                        DatePicker::make('created_until')->label('Hasta:'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
           
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVisitadors::route('/'),
            'create' => Pages\CreateVisitador::route('/create'),
            'edit' => Pages\EditVisitador::route('/{record}/edit'),
        ];
    }
}
