<?php

namespace App\Filament\Resources\VisitaResource\Pages;

use App\Filament\Resources\VisitaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;
class CreateVisita extends CreateRecord
{
    
    protected static string $resource = VisitaResource::class;

    #[On('location-updated')]
    public function updateCoordinates($lat, $lng)
    {
        // Actualiza las propiedades directamente
       
        dd($lat);
        $this->form->fill([
            'latitud' => $lat,
            'longitude' => $lng,
        ]);
    }
}
