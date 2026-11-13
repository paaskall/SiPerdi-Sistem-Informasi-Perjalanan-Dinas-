<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada
            if (!Schema::hasColumn('users', 'nama_panggilan')) {
                $table->string('nama_panggilan', 50)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'pangkat')) {
                $table->string('pangkat', 100)->nullable()->after('jabatan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_panggilan', 'pangkat']);
        });
    }
};