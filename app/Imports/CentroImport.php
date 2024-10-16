<?php

namespace App\Imports;

use App\Models\Centro;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class CentroImport implements ToModel,WithHeadingRow
{
    public $rowCount = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
         // Verificar si el campo dirección está vacío y asignar "No tiene" si es así
        $direccion = !empty($row['direccion']) ? $row['direccion'] : 'No tiene';
        // Verificar si el centro ya existe, si no, crearla
        [$centro, $created] = Centro::firstOrCreate(
            ['nombre' => $row['nombre']],  // Condición de búsqueda
            ['direccion' => $direccion, 'estado' => true]  // Valor por defecto si no existe
   );

            // Incrementar el contador de filas importadas solo si se creó una nueva especialidad
                if ($created) {
                       $this->rowCount++;
   }

               return $centro;
   }
   
   public function getRowCount(): int
   {
       return $this->rowCount;
   }
}
