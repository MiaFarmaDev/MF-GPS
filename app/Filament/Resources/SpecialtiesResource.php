<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialtiesResource\Pages;
use App\Filament\Resources\SpecialtiesResource\RelationManagers;
use App\Models\Specialties;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;

class SpecialtiesResource extends Resource
{  
    // protected static ?string $title = 'Custom Page Title';
    protected static ?string $model = Specialties::class;
  
    protected static ?string $heading='Especialidadessss';
    protected static ?string $navigationLabel='Especialidades';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
   
 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nombre')
                    ->required()
                    ->alpha()
                    ->unique('specialties', 'name', ignoreRecord: true) // Verifica la unicidad ignorando el registro actual en modo edición
                    ->maxLength(30)->validationMessages([
                        'alpha'=>'El campo nombre solo debe tener letras',
                        'unique'=>'Ya existe una especialidad con ese nombre',
                    ]),
                Forms\Components\Toggle::make('status')->label('Estado')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table 
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->label('Nombre'),
                Tables\Columns\ToggleColumn::make('status')
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
            ->filters([
                    SelectFilter::make('status')->label('Estado')
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
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                // Action::make('Desactivar')->icon('heroicon-m-trash')->color('danger')
                // ->requiresConfirmation()
                // ->action(function (Specialties $record){
                //     $record->status= false;
                //     $record->save();
                // })->visible(fn (Specialties $record):bool=>$record->status),
                
               
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
            'index' => Pages\ListSpecialties::route('/'),
            'create' => Pages\CreateSpecialties::route('/create'),
            'edit' => Pages\EditSpecialties::route('/{record}/edit'),
        ];
    }
}
