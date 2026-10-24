<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_atencion', function (Blueprint $table) {
            $table->id('id_horario');

            $table->foreignId('id_medico')
                ->constrained('medicos', 'id_medico');

            $table->smallInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->smallInteger('duracion_cita_min')->default(30);
            $table->string('consultorio', 40);
            $table->boolean('estado')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_atencion');
    }
};