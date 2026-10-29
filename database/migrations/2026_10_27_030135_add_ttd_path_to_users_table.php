<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek dulu apakah kolom ttd_path sudah ada (dari Sprint 0)
            // Jika belum ada, tambahkan
            if (!Schema::hasColumn('users', 'ttd_path')) {
                $table->string('ttd_path')->nullable()->after('golongan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ttd_path');
        });
    }
};