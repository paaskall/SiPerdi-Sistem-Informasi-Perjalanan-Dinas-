<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Golongan;
use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        Divisi::firstOrCreate(['kode' => 'UMUM'], ['nama' => 'Umum']);
        Divisi::firstOrCreate(['kode' => 'KEU'], ['nama' => 'Keuangan']);

        Jabatan::firstOrCreate(['nama' => 'Staf'], ['level' => 1]);
        Jabatan::firstOrCreate(['nama' => 'Kepala Bagian'], ['level' => 2]);
        Jabatan::firstOrCreate(['nama' => 'Kepala Instansi'], ['level' => 3]);

        foreach (['Gol I', 'Gol II', 'Gol III', 'Gol IV'] as $g) {
            Golongan::firstOrCreate(['nama' => $g]);
        }
    }
}