<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id('id_bitacora');
            $table->string('tabla_afectada', 50);
            $table->unsignedBigInteger('id_registro_afectado');
            $table->string('accion', 50);
            $table->text('valores_anteriores')->nullable();
            $table->text('valores_nuevos')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();

            $table->foreignId('id_usuario')
                ->constrained('usuarios', 'id_usuario')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};