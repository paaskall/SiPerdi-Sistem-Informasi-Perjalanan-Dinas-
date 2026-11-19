<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akuns', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();   // contoh: 086.01.CO.4825.ABQ.001.051.A.524111
            $table->string('nama', 255);            // contoh: Belanja Perjalanan Dinas
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akuns');
    }
};