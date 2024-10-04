<?php

namespace App\Filament\Resources\SpecialtiesResource\Pages;

use App\Filament\Resources\SpecialtiesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialties extends CreateRecord
{
    protected static string $resource = SpecialtiesResource::class;
    protected static ?string $title = 'Crear Especialidad';
}
