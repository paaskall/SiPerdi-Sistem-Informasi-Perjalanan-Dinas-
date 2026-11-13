<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_perjalanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjalanan_id')->constrained('perjalanan_dinas')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('users')->cascadeOnDelete();
            
            // Isi Laporan
            $table->text('ringkasan_kegiatan');
            $table->text('hasil_perjalanan');
            $table->text('kendala')->nullable();
            $table->decimal('total_biaya_riil', 15, 2)->default(0);
            
            // File Lampiran (Bukti / Dokumentasi)
            $table->string('file_lampiran')->nullable();
            
            // Status: DRAFT, SUBMITTED, RECEIVED_BY_FINANCE
            $table->string('status', 30)->default('SUBMITTED');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_perjalanans');
    }
};