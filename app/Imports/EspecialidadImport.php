<?php

namespace App\Imports;

use App\Models\Specialties;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EspecialidadImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {


// crear la especialidad 
        return new Specialties([
            'name' => $row['especialidad'],
            'status' =>  true,  // Estado predeterminado a true si no está en el Excel
        ]);
    }
}
