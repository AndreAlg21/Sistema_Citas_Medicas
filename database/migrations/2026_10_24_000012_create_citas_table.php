<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id('id_cita');
            $table->string('codigo', 12)->unique();

            $table->foreignId('id_paciente')
                ->constrained('pacientes', 'id_paciente');

            $table->foreignId('id_medico')
                ->constrained('medicos', 'id_medico');

            $table->foreignId('id_especialidad')
                ->constrained('especialidades', 'id_especialidad');

            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('modalidad', 12);
            $table->string('motivo', 200)->nullable();
            $table->string('estado', 15);
            $table->string('motivo_cancelacion', 200)->nullable();

            $table->foreignId('id_usuario_registra')
                ->constrained('usuarios', 'id_usuario');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};