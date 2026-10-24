<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recordatorios_cita', function (Blueprint $table) {
            $table->id('id_recordatorio');

            $table->foreignId('id_cita')
                ->constrained('citas', 'id_cita');

            $table->timestamp('fecha_programada');
            $table->timestamp('fecha_envio')->nullable();
            $table->string('estado', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recordatorios_cita');
    }
};