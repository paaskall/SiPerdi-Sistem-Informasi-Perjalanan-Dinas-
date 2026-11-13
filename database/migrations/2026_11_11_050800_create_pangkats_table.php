<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pangkats', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('golongan_ruang', 20)->nullable(); // Contoh: III/a
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pangkats');
    }
};