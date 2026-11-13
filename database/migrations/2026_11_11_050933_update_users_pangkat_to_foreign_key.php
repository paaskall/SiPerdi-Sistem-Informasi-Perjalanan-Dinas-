<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop kolom string lama (jika ada)
            if (Schema::hasColumn('users', 'pangkat')) {
                $table->dropColumn('pangkat');
            }
            // Tambah kolom pangkat_id (foreign key)
            if (!Schema::hasColumn('users', 'pangkat_id')) {
                $table->foreignId('pangkat_id')->nullable()->after('golongan_id')->constrained('pangkats')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pangkat_id']);
            $table->dropColumn('pangkat_id');
            $table->string('pangkat', 100)->nullable();
        });
    }
};