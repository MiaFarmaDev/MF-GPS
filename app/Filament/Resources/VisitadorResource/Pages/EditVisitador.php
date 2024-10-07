<?php

namespace App\Filament\Resources\VisitadorResource\Pages;

use App\Filament\Resources\VisitadorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisitador extends EditRecord
{
    protected static string $resource = VisitadorResource::class;
    protected static ?string $title = 'Editar Visitador';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
