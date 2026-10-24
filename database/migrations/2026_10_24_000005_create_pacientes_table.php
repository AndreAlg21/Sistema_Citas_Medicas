<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id('id_paciente');
            $table->string('codigo', 12)->unique();

            $table->foreignId('id_usuario')
                ->nullable()
                ->unique()
                ->constrained('usuarios', 'id_usuario')
                ->nullOnDelete();

            $table->string('ci', 20)->nullable()->unique();
            $table->string('nombres', 80);
            $table->string('apellidos', 80);
            $table->date('fecha_nacimiento');
            $table->string('sexo', 15)->nullable();
            $table->string('telefono', 20);
            $table->string('correo', 120)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->boolean('acepta_notificaciones')->default(true);
            $table->boolean('acepta_privacidad')->default(false);
            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};