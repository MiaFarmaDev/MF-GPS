<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicoResource\Pages;
use App\Filament\Resources\MedicoResource\RelationManagers;
use App\Models\Medico;
use App\Models\Centro;
use App\Models\Visitador;
use App\Models\Specialties;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MedicosImport;
use Illuminate\Support\Facades\Storage;
class MedicoResource extends Resource
{
    protected static ?string $navigationGroup='Recursos Médicos';
    protected static ?int $navigationSort=4;
    protected static ?string $model = Medico::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('celular')
                    ->required()
                    ->unique( ignoreRecord:true)
                    ->numeric()
                    ->maxLength(10)->validationMessages([
                        'required' => 'Este campo es obligatorio.',
                        'unique' => 'Este celular ya se encuentra registrado.',
                        'numeric' => 'Este campo solo acepta números.',
                    ]),
                Forms\Components\Select::make('centro_id')
                ->label('Centro')
                ->required()
                ->options(Centro::where('estado',true)->pluck('nombre', 'id')) // Pluck para obtener id y nombre
                ->searchable()->validationMessages([
                    'required' => 'Este campo es obligatorio.',
                ]),
                Forms\Components\Select::make('specialty_id')
                ->label('Especialidad')
                    ->required()
                    ->options(Specialties::where('status',true)->pluck('name', 'id')) // Pluck para obtener id y nombre
                ->searchable()->validationMessages([
                    'required' => 'Este campo es obligatorio.',
                ]), 
                Forms\Components\Select::make('visitador_id')
                ->label('Visitador')
                    ->required()
                    ->options(Visitador::where('estado',true)->pluck('nombre','id'))
                    ->searchable()->validationMessages([
                        'required' => 'Este campo es obligatorio.',
                    ]),
                Forms\Components\Toggle::make('estado')
                    ->required()->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                ->sortable() ->searchable(),
                Tables\Columns\TextColumn::make('celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('centro.nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('especialidad.name')
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('visitador.nombre')
                    ->sortable()->searchable(),
                Tables\Columns\ToggleColumn::make('estado'),
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
                        DatePicker::make('created_from')->label('Desde:')->maxDate(now()) 
                        ->reactive(),
                        DatePicker::make('created_until')
                            ->label('Hasta:')
                            ->minDate(fn (callable $get) => $get('created_from')) // Establece el valor mínimo dinámico, dependiendo del valor de 'created_from'
                            ->maxDate(now()) // Fecha máxima como la actual
                            ->reactive(),    // Hace que este campo se reactive cuando cambie 'created_from'
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
            ->headerActions([
                Action::make('import')
                    ->label('Importar')
                    ->icon('heroicon-o-users')
                    ->form([
                        FileUpload::make('file')
                            ->label('Selecciona un archivo Excel')
                            ->disk('local') // Usa el almacenamiento local
                            ->directory('uploads/excels') // Carpeta donde se guardará el archivo
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) // Solo archivos Excel
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // El archivo ha sido guardado en 'uploads/excels' en el disco 'local'
                        $filePath = Storage::disk('local')->path($data['file']);

                        // Lógica de importación utilizando el archivo guardado
                        Excel::import(new MedicosImport, $filePath);

                        // Notificación de éxito
                        Filament::notify('success', 'Datos importados correctamente.');

                        // Eliminar el archivo después de la importación, si lo deseas
                        Storage::disk('local')->delete($data['file']);
                    }),
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
            'index' => Pages\ListMedicos::route('/'),
            'create' => Pages\CreateMedico::route('/create'),
            'edit' => Pages\EditMedico::route('/{record}/edit'),
        ];
    }
     // Función para manejar la importación de datos
     protected function importData($file)
     {
         // Usa la librería Excel para importar los datos
         Excel::import(new MedicosImport, $file);
     }
}
