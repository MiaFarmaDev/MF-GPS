<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;
    protected $fillable=['nombre','celular','centro_id','specialty_id','visitador_id','estado',];
    // Un médico pertenece a un centro
    public function centro()
    {
        return $this->belongsTo(Centro::class);
    }

    // Un médico pertenece a una especialidad
    public function especialidad()
    {
        return $this->belongsTo(Specialties::class,'specialty_id');
    }

    // Un médico pertenece a un visitador
    public function visitador()
    {
        return $this->belongsTo(Visitador::class);
    }
}
