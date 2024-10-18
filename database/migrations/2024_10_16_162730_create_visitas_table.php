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
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('cascade'); 
            $table->foreignId('visitador_id')->constrained('visitadors')->onDelete('cascade'); 
            $table->decimal('latitud',10,8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('producto')->nullable();
            $table->string('observacion')->nullable();
            $table->string('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
