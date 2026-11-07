<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios_atencion', function (Blueprint $table): void {
            $table->smallInteger('duracion_cita_min')->default(15)->change();
        });

        DB::table('horarios_atencion')->update(['duracion_cita_min' => 15]);
    }

    public function down(): void
    {
        Schema::table('horarios_atencion', function (Blueprint $table): void {
            $table->smallInteger('duracion_cita_min')->default(30)->change();
        });
    }
};
