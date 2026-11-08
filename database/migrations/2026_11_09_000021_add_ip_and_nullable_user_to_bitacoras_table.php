<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bitacoras', function (Blueprint $table): void {
            $table->dropForeign(['id_usuario']);
            $table->unsignedBigInteger('id_usuario')->nullable()->change();
            $table->string('direccion_ip', 45)->nullable()->after('id_usuario');
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bitacoras', function (Blueprint $table): void {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('direccion_ip');
            $table->unsignedBigInteger('id_usuario')->nullable(false)->change();
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->cascadeOnDelete();
        });
    }
};
