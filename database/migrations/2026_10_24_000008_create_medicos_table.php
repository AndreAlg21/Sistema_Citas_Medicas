<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id('id_medico');

            $table->string('codigo', 12)->unique();

            $table->foreignId('id_usuario')
                ->nullable()
                ->unique()
                ->constrained('usuarios', 'id_usuario')
                ->nullOnDelete();

            $table->string('registro_profesional', 30)->unique();
            $table->string('nombres', 80);
            $table->string('apellidos', 80);
            $table->string('telefono', 20);
            $table->string('correo', 120);
            $table->boolean('puede_marcar_atendida')->default(false);
            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};