<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloqueos_agenda', function (Blueprint $table) {
            $table->id('id_bloqueo');

            $table->foreignId('id_medico')
                ->nullable()
                ->constrained('medicos', 'id_medico')
                ->nullOnDelete();

            $table->date('fecha');
            $table->string('motivo', 150);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloqueos_agenda');
    }
};