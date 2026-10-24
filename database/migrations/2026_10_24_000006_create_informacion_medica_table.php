<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informacion_medica', function (Blueprint $table) {
            $table->id('id_info');
            $table->foreignId('id_paciente')->unique();
            $table->string('tipo_sangre', 5)->nullable();
            $table->text('alergias')->nullable();
            $table->text('enfermedades_cronicas')->nullable();
            $table->text('medicamentos')->nullable();
            $table->text('antecedentes')->nullable();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('id_paciente')
                ->references('id_paciente')
                ->on('pacientes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informacion_medica');
    }
};