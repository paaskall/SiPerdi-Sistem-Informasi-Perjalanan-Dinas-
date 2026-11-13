<?php

namespace Database\Seeders;

use App\Models\Pangkat;
use Illuminate\Database\Seeder;

class PangkatSeeder extends Seeder
{
    public function run(): void
    {
        $pangkats = [
            ['nama' => 'Juru Muda', 'golongan_ruang' => 'I/a'],
            ['nama' => 'Juru Muda Tingkat I', 'golongan_ruang' => 'I/b'],
            ['nama' => 'Juru', 'golongan_ruang' => 'I/c'],
            ['nama' => 'Juru Tingkat I', 'golongan_ruang' => 'I/d'],
            ['nama' => 'Pengatur Muda', 'golongan_ruang' => 'II/a'],
            ['nama' => 'Pengatur Muda Tingkat I', 'golongan_ruang' => 'II/b'],
            ['nama' => 'Pengatur', 'golongan_ruang' => 'II/c'],
            ['nama' => 'Pengatur Tingkat I', 'golongan_ruang' => 'II/d'],
            ['nama' => 'Penata Muda', 'golongan_ruang' => 'III/a'],
            ['nama' => 'Penata Muda Tingkat I', 'golongan_ruang' => 'III/b'],
            ['nama' => 'Penata', 'golongan_ruang' => 'III/c'],
            ['nama' => 'Penata Tingkat I', 'golongan_ruang' => 'III/d'],
            ['nama' => 'Pembina', 'golongan_ruang' => 'IV/a'],
            ['nama' => 'Pembina Tingkat I', 'golongan_ruang' => 'IV/b'],
            ['nama' => 'Pembina Utama Muda', 'golongan_ruang' => 'IV/c'],
            ['nama' => 'Pembina Utama Madya', 'golongan_ruang' => 'IV/d'],
            ['nama' => 'Pembina Utama', 'golongan_ruang' => 'IV/e'],
        ];

        foreach ($pangkats as $p) {
            Pangkat::firstOrCreate($p);
        }
    }
}