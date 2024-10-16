<?php

namespace App\Imports;

use App\Models\Medico;
use App\Models\Centro;
use App\Models\Visitador;
use App\Models\Specialties;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithChunkReading;


class MedicosImport implements ToModel,WithHeadingRow,WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public $rowCount = 0;
    protected $currentRow = 1; // Iniciar en 1 si tienes encabezado


    public function model(array $row)
    {
        // // Verificar si el campo 'centro' está vacío y asignar un valor predeterminado
        // if (empty($row['centro'])) {
        //     dd("El campo 'centro' está vacío en la fila: {$this->currentRow}");  // Mostrar el número de la fila actual
        // }

        $nombreCentro = $row['centro'] ?? 'CENTRO DESCONOCIDO';
        

        // Verificar si el Centro ya existe, si no, crearlo
        $centro = Centro::firstOrCreate(
            ['nombre' => $nombreCentro],  // Condición de búsqueda
            [
                'nombre' => $nombreCentro,
                'direccion' => 'NO TIENE',
                'estado' => true,
            ]   // Valores a insertar si no existe
        );

        // Verificar si la Especialidad ya existe, si no, crearla
        $specialty = Specialties::firstOrCreate(
            ['name' => $row['especialidad'] ?? 'ESPECIALIDAD DESCONOCIDA'],  // Condición de búsqueda
            ['name' => $row['especialidad'] ?? 'ESPECIALIDAD DESCONOCIDA', 'status' => true]  
        );

        // Verificar si el Visitador ya existe, si no, crearlo
        $visitador = Visitador::firstOrCreate(
            ['nombre' => $row['visitador'] ?? 'VISITADOR DESCONOCIDO'],  // Condición de búsqueda
            [
                'nombre' => $row['visitador'] ?? 'VISITADOR DESCONOCIDO',
                'correo' => '',
                'celular' => 'NO TIENE',
                'estado' => true
            ]  
        );

        // Verificar si el Médico ya existe, si no, crearlo
        [$medico, $created] = Medico::firstOrCreate(
            ['nombre' => $row['medico'] ?? 'MEDICO DESCONOCIDO'],  // Condición de búsqueda
            [
                'nombre' => $row['medico'] ?? 'MEDICO DESCONOCIDO',
                'celular' => 'NO TIENE',
                'centro_id' => $centro->id,
                'specialty_id' => $specialty->id,
                'visitador_id' => $visitador->id,
                'estado' => true
            ]
        );

        // Incrementar el contador de filas importadas solo si se creó un médico
        if ($created) {
            $this->rowCount++;
        }

        return $medico;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
    // Método de chunk para leer de a 100 filas
    public function chunkSize(): int
    {
        return 100;  // Procesar de a 100 filas por vez
    }
}
