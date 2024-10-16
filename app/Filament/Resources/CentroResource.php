<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CentroResource\Pages;
use App\Filament\Resources\CentroResource\RelationManagers;
use App\Models\Centro;
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CentroImport;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;


class CentroResource extends Resource
{
    protected static ?string $model = Centro::class;
    protected static ?int $navigationSort=2;
    protected static ?string $navigationGroup='Recursos Médicos';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->alpha()
                    ->maxLength(255)->validationMessages([
                        'alpha'=>'El campo nombre solo debe tener letras',
                        
                    ])->dehydrateStateUsing(fn($state) => strtoupper($state)), // Convierte a mayúsculas
                Forms\Components\Textarea::make('direccion')
                    ->required()
                    ->columnSpanFull()->dehydrateStateUsing(fn($state) => strtoupper($state)),
                Forms\Components\Toggle::make('estado')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('direccion')
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

                         // Crear una instancia de la importación
                        $import = new CentroImport;
                         // Realizar la importación
                        Excel::import($import, $filePath);
                         // Obtener la cantidad de filas importadas
                            $importedRows = $import->getRowCount();

                         // Mensaje condicional según la cantidad de registros importados
                            if ($importedRows > 0) {
                                // Notificación de éxito con la cantidad de datos importados
                                Notification::make()
                                    ->title('Datos importados correctamente.')
                                    ->body("Se importaron {$importedRows} registros.")
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('success')
                                    ->send();
                            } else {
                                // Notificación indicando que no se importaron registros
                                Notification::make()
                                    ->title('No se importaron registros.')
                                    ->body('No se encontraron nuevos registros para importar.')
                                    ->icon('heroicon-o-exclamation-circle')
                                    ->iconColor('warning')
                                    ->send();
                            }
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
            'index' => Pages\ListCentros::route('/'),
            'create' => Pages\CreateCentro::route('/create'),
            'edit' => Pages\EditCentro::route('/{record}/edit'),
        ];
    }
}
