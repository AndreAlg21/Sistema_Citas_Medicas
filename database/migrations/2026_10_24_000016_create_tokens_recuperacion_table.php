<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens_recuperacion', function (Blueprint $table) {
            $table->id('id_token');
            $table->string('correo', 120)->index();
            $table->string('token_hash', 255);
            $table->timestamp('expira_en');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens_recuperacion');
    }
};