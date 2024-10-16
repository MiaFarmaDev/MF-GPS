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
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EspecialidadImport;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class SpecialtiesResource extends Resource
{  
    protected static ?string $navigationGroup='Recursos Médicos';
    protected static ?int $navigationSort=1;
    protected static ?string $model = Specialties::class;
  
    protected static ?string $heading='Especialidadessss';
    protected static ?string $navigationLabel='Especialidades';
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
   
 

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->label('Nombre')
                ->required()
                ->unique('specialties', 'name', ignoreRecord: true) // Verifica la unicidad ignorando el registro actual en modo edición
                ->maxLength(30)
                ->validationMessages([
                    // 'alpha' => 'El campo nombre solo debe tener letras',
                    'unique' => 'Ya existe una especialidad con ese nombre',
                ])
                ->dehydrateStateUsing(fn($state) => strtoupper($state)), // Convierte a mayúsculas
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
                            ->required()
                            ->validationMessages(
                                [
                                    'required'=>'Debe seleciconar un archivo .xlsx'
                                ]
                            ),
                    ])
                    ->action(function (array $data) {
                        // El archivo ha sido guardado en 'uploads/excels' en el disco 'local'
                        $filePath = Storage::disk('local')->path($data['file']);

                        // Lógica de importación utilizando el archivo guardado
                        // Excel::import(new EspecialidadImport, $filePath);
                         // Crear una instancia de la importación
                        $import = new EspecialidadImport;
                         // Realizar la importación
                        Excel::import($import, $filePath);
                         // Obtener la cantidad de filas importadas
                            $importedRows = $import->getRowCount();

                        // Notificación de éxito
                        Notification::make()
                        ->title('Datos importados correctamente.')
                        ->body("Se importaron {$importedRows} registros.")
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();

                        // Eliminar el archivo después de la importación
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
            'index' => Pages\ListSpecialties::route('/'),
            'create' => Pages\CreateSpecialties::route('/create'),
            'edit' => Pages\EditSpecialties::route('/{record}/edit'),
        ];
    }
}
