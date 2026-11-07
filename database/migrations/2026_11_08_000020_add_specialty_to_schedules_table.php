<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios_atencion', function (Blueprint $table): void {
            $table->foreignId('id_especialidad')
                ->nullable()
                ->after('id_medico')
                ->constrained('especialidades', 'id_especialidad')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('horarios_atencion', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('id_especialidad');
        });
    }
};
