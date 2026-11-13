<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            $table->string('no_pembukuan', 50)->nullable()->after('nomor_pengajuan');
            $table->string('akun', 100)->nullable()->after('no_pembukuan');
            $table->string('no_spd', 100)->nullable()->after('akun');
            $table->string('tempat_berangkat', 100)->nullable()->after('kota_tujuan_id');
            $table->string('alat_angkutan', 50)->nullable()->after('tempat_berangkat');
            $table->char('tingkat_biaya', 1)->default('C')->after('alat_angkutan');
            $table->string('nama_bendahara', 100)->nullable();
            $table->string('nip_bendahara', 30)->nullable();
            $table->string('nama_ppk', 100)->nullable();
            $table->string('nip_ppk', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            $table->dropColumn([
                'no_pembukuan', 'akun', 'no_spd', 'tempat_berangkat',
                'alat_angkutan', 'tingkat_biaya', 'nama_bendahara', 'nip_bendahara',
                'nama_ppk', 'nip_ppk',
            ]);
        });
    }
};