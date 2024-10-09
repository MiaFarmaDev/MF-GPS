<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('celular');
            $table->foreignId('centro_id')->constrained('centros')->onDelete('cascade'); // Relación con el modelo Centro
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('cascade'); // Relación con el modelo Especialidad
            $table->foreignId('visitador_id')->constrained('visitadors')->onDelete('cascade'); // Relación con el modelo Visitador
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
