<?php

namespace App\Filament\Resources\VisitaResource\Pages;

use App\Filament\Resources\VisitaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVisita extends CreateRecord
{
    protected static string $resource = VisitaResource::class;


    
    // Sobrescribir el método mount() para inyectar el script
    public function mount()
    {
        parent::mount();

        // Compartir la vista con el script JavaScript
        view()->share('script', view('filament.customs.verificar-ubicacion'));
    }

   // Asegúrate de que este método sea público
   public function scriptview(): ?View
    {
        return view('filament.customs.verificar-ubicacion');
    }
   // Este método es necesario para manejar la acción de verificación de ubicación
//    public function verificarUbicacion()
//    {    
//     // dd('hols');
//        // Inyectamos el código JavaScript para obtener la geolocalización
//        echo "
//        <script>
//            if ('geolocation' in navigator) {
//                navigator.geolocation.getCurrentPosition(function(position) {
//                    // Obtener latitud y longitud
//                    var latitud = position.coords.latitud;
//                    var longitude = position.coords.longitude;

//                    // Asignar los valores a los campos del formulario
//                    document.getElementById('latitud').value = latitud;
//                    document.getElementById('longitude').value = longitude;
//                    console.log('hola');
//                }, function(error) {
//                    alert('Error al obtener la ubicación: ' + error.message);
//                });
//            } else {
//                alert('Geolocalización no está disponible en este navegador');
//            }
//        </script>";
//    }
}
