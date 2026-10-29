<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjalanan_id')->constrained('perjalanan_dinas')->cascadeOnDelete();
            $table->string('nomor_reimb', 50)->unique();
            $table->decimal('total_realisasi', 15, 2)->default(0);
            $table->decimal('selisih', 15, 2)->default(0); // Realisasi - Estimasi
            
            // Status: SUBMITTED, FINANCE_APPROVED, PIMPINAN_SIGNED, REJECTED
            $table->string('status', 30)->default('SUBMITTED');
            
            $table->foreignId('finance_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finance_approved_at')->nullable();
            $table->text('catatan_keuangan')->nullable();

            $table->foreignId('pimpinan_signed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pimpinan_signed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reimbursement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_id')->constrained('reimbursements')->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('coas')->cascadeOnDelete();
            $table->string('keterangan')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->string('file_struk')->nullable(); // Path ke gambar struk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursement_items');
        Schema::dropIfExists('reimbursements');
    }
};