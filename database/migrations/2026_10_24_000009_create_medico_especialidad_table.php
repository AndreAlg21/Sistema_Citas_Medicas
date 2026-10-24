<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico_especialidad', function (Blueprint $table) {
            $table->foreignId('id_medico')
                ->constrained('medicos', 'id_medico');

            $table->foreignId('id_especialidad')
                ->constrained('especialidades', 'id_especialidad');

            $table->primary(['id_medico', 'id_especialidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_especialidad');
    }
};