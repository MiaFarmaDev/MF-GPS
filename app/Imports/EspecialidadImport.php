<?php

namespace App\Imports;

use App\Models\Specialties;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EspecialidadImport implements ToModel,WithHeadingRow
{
    public $rowCount = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
           // Verificar si la Especialidad ya existe, si no, crearla
            [$specialty, $created] = Specialties::firstOrCreate(
             ['name' => $row['especialidad']],  // Condición de búsqueda
             ['status' => true]  // Valor por defecto para 'status' si no existe
    );

             // Incrementar el contador de filas importadas solo si se creó una nueva especialidad
                 if ($created) {
                        $this->rowCount++;
    }

                return $specialty;
    }
    
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
