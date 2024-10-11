<?php

namespace App\Imports;

use App\Models\Medico;
use App\Models\Centro;
use App\Models\Visitador;
use App\Models\Specialties;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicosImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        
        // Verificar si el Centro ya existe, si no, crearlo
        $centro = Centro::firstOrCreate(
            ['nombre' => $row['centro']],  // Condición de búsqueda
            ['nombre' => $row['centro']]   // Valores a insertar si no existe
        );

        // Verificar si la Especialidad ya existe, si no, crearla
        $specialty = Specialties::firstOrCreate(
            ['name' => $row['especialidad']],  // Condición de búsqueda
            ['name' => $row['especialidad']]   // Valores a insertar si no existe
        );

        // Verificar si el Visitador ya existe, si no, crearlo
        $visitador = Visitador::firstOrCreate(
            ['nombre' => $row['visitador']],  // Condición de búsqueda
            ['nombre' => $row['visitador']]   // Valores a insertar si no existe
        );

        // Ahora podemos crear el médico con las referencias correctas
        return new Medico([
            'nombre' => $row['nombre'],
            'centro_id' => $centro->id,
            'specialty_id' => $specialty->id,
            'visitador_id' => $visitador->id,
            'estado' =>  true,  // Estado predeterminado a true si no está en el Excel
        ]);
    
    }
}
