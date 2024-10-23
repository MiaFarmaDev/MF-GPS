<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;
    protected $fillable=['medico_id','visitador_id','latitud',
    'longitude','producto','observacion','estado'];
}
