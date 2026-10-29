<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $divisi = Divisi::where('kode', 'KEU')->first();
        $jabatan = Jabatan::where('nama', 'Kepala Bagian')->first();
        $golongan = Golongan::where('nama', 'Gol III')->first();

        $admin = User::firstOrCreate(
            ['nip' => '198001012005011001'],
            [
                'name' => 'Admin Keuangan',
                'username' => 'adminkeu',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'divisi_id' => $divisi?->id,
                'jabatan_id' => $jabatan?->id,
                'golongan_id' => $golongan?->id,
            ]
        );
        $admin->assignRole('keuangan');
    }
}