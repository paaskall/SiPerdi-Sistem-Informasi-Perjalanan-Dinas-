<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perjalanan_biaya_estimasi', function (Blueprint $table) {
            $table->integer('qty')->default(1)->after('keterangan');
            $table->string('satuan', 20)->default('kali')->after('qty');
            $table->decimal('harga_satuan', 15, 2)->nullable()->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('perjalanan_biaya_estimasi', function (Blueprint $table) {
            $table->dropColumn(['qty', 'satuan', 'harga_satuan']);
        });
    }
};