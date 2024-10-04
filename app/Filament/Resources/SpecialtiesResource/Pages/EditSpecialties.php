<?php

namespace App\Filament\Resources\SpecialtiesResource\Pages;

use App\Filament\Resources\SpecialtiesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecialties extends EditRecord
{
    protected static string $resource = SpecialtiesResource::class;
    protected static ?string $title = 'Editar especialidad';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
   
}
