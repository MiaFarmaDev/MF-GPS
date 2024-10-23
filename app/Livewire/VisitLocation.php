<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Filament\Resources\VisitaResource;
use App\Filament\Resources\VisitaResource\Pages\CreateVisita;

class VisitLocation extends Component
{
    public $latitud;
    public $longitude;

    #[On('location-updated')]
    public function updateCoordinates($lat, $lng)
    {   dd($lat,$lng);
        $this->latitud = $lat;
        $this->longitude = $lng;
        $this->dispatch('location-updated', latitud: $lat)->to(CreateVisita::class);
    }
    public function updateLatitud($lat, $lng)
    {   
        // dd($lat,$lng);
        $this->latitud = $lat;
        $this->longitude = $lng;
        $this->dispatch('location-updated', latitud: $lat)->to(CreateVisita::class);
    }
    public function render()
    {
        return view('livewire.visit-location');
    }
}
