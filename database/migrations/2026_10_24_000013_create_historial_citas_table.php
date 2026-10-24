<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_citas', function (Blueprint $table) {
            $table->id('id_historial');

            $table->foreignId('id_cita')
                ->constrained('citas', 'id_cita');

            $table->string('evento', 20);
            $table->string('estado_anterior', 15)->nullable();
            $table->string('estado_nuevo', 15);
            $table->date('fecha_anterior')->nullable();
            $table->time('hora_anterior')->nullable();
            $table->string('detalle', 250)->nullable();

            $table->foreignId('id_usuario')
                ->nullable()
                ->constrained('usuarios', 'id_usuario')
                ->nullOnDelete();

            $table->timestamp('fecha_evento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_citas');
    }
};