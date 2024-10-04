<?php

namespace App\Filament\Resources\SpecialtiesResource\Pages;

use App\Filament\Resources\SpecialtiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecialties extends ListRecords
{
    protected static string $resource = SpecialtiesResource::class;
    protected static ?string $title = 'Especialidades';
    
   

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Crear especialidad'),
        ];
    }
}
