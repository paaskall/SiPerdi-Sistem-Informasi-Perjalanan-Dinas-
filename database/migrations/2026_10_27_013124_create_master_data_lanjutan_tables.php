<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Kota
        Schema::create('kotas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('provinsi')->nullable();
            $table->timestamps();
        });

        // Tabel Tarif Harian (Uang Harian per Kota per Golongan)
        Schema::create('tarif_haris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kota_id')->constrained('kotas')->cascadeOnDelete();
            $table->foreignId('golongan_id')->constrained('golongans')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
            
            // 1 kota + 1 golongan hanya boleh punya 1 tarif
            $table->unique(['kota_id', 'golongan_id']);
        });

        // Tabel Plafon Biaya (Penginapan, Transport, dll)
        Schema::create('plafon_biayas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('golongan_id')->constrained('golongans')->cascadeOnDelete();
            $table->enum('komponen', ['penginapan', 'transport', 'lainnya']);
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
            
            $table->unique(['golongan_id', 'komponen']);
        });

        // Tabel COA (Chart of Accounts)
        Schema::create('coas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Tabel Template Kop Surat
        Schema::create('kop_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['upload', 'html'])->default('upload');
            $table->string('gambar_kop_path')->nullable();
            $table->longText('html_template')->nullable();
            $table->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kop_templates');
        Schema::dropIfExists('coas');
        Schema::dropIfExists('plafon_biayas');
        Schema::dropIfExists('tarif_haris');
        Schema::dropIfExists('kotas');
    }
};