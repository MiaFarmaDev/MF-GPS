<?php

namespace App\Filament\Resources\VisitadorResource\Pages;

use App\Filament\Resources\VisitadorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisitadors extends ListRecords
{
    protected static string $resource = VisitadorResource::class;
    protected static ?string $title = 'Visitadores';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
