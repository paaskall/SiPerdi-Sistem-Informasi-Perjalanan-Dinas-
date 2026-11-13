<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            // Kolom baru untuk alur UMK yang disederhanakan
            if (!Schema::hasColumn('perjalanan_dinas', 'printed_at')) {
                $table->timestamp('printed_at')->nullable()->after('catatan_keuangan');
            }
            if (!Schema::hasColumn('perjalanan_dinas', 'received_by_finance_at')) {
                $table->timestamp('received_by_finance_at')->nullable()->after('printed_at');
            }
            if (!Schema::hasColumn('perjalanan_dinas', 'received_by_finance_id')) {
                $table->foreignId('received_by_finance_id')->nullable()->after('received_by_finance_at')->constrained('users')->nullOnDelete();
            }
        });

        // Update ENUM status (jika pakai ENUM, kita ubah via raw SQL karena Laravel tidak support drop ENUM)
        // Karena di struktur awal status adalah string, kita TIDAK perlu ubah apa-apa.
        // Nilai baru yang akan dipakai: DRAFT, PRINTED, RECEIVED_BY_FINANCE
    }

    public function down(): void
    {
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            $table->dropForeign(['received_by_finance_id']);
            $table->dropColumn(['printed_at', 'received_by_finance_at', 'received_by_finance_id']);
        });
    }
};