<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos_emergencia', function (Blueprint $table) {
            $table->id('id_contacto');

            $table->foreignId('id_paciente')
                ->constrained('pacientes', 'id_paciente');

            $table->string('nombre', 120);
            $table->string('parentesco', 40);
            $table->string('telefono', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos_emergencia');
    }
};