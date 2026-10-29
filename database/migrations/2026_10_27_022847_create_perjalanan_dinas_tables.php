<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Utama Pengajuan
        Schema::create('perjalanan_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 50)->unique();
            $table->foreignId('pegawai_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('divisi_id')->constrained('divisis')->cascadeOnDelete();
            $table->foreignId('kota_tujuan_id')->constrained('kotas')->cascadeOnDelete();
            $table->date('tanggal_berangkat');
            $table->date('tanggal_pulang');
            $table->integer('jumlah_hari');
            $table->text('maksud_perjalanan');
            $table->decimal('total_estimasi', 15, 2)->default(0);
            
            // Status: DRAFT, SUBMITTED, FINANCE_APPROVED, REJECTED
            $table->string('status', 30)->default('DRAFT');
            
            $table->foreignId('finance_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finance_approved_at')->nullable();
            $table->text('catatan_keuangan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Rincian Estimasi Biaya
        Schema::create('perjalanan_biaya_estimasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjalanan_id')->constrained('perjalanan_dinas')->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('coas')->cascadeOnDelete();
            $table->string('keterangan')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perjalanan_biaya_estimasi');
        Schema::dropIfExists('perjalanan_dinas');
    }
};