<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');

            $table->foreignId('id_rol')
                ->constrained('roles', 'id_rol');

            $table->string('nombre_completo', 120);
            $table->string('nombre_usuario', 40)->unique();
            $table->string('correo', 120)->unique();
            $table->string('contrasena', 255);
            $table->string('telefono', 20)->nullable();
            $table->boolean('estado')->default(true);
            $table->smallInteger('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();
            $table->timestamp('ultimo_acceso')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};