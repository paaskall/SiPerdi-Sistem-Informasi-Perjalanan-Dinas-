<?php

namespace App\Actions\Perjalanan;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateManualPegawaiAction
{
    /**
     * Buat user baru dari input manual super admin.
     * Return User|null (null kalau ada error).
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function execute(
        string $nama,
        ?string $nip,
        ?int $golonganId,
        ?int $divisiId,
    ): User {
        $username = $this->generateUsername($nip, $nama);

        $pegawai = User::create([
            'name' => $nama,
            'nip' => $nip ?: null,
            'username' => $username,
            'email' => $username . '@siperdi.local',
            'password' => Hash::make('password123'),
            'golongan_id' => $golonganId,
            'divisi_id' => $divisiId ?: null,
        ]);

        $pegawai->assignRole('pegawai');

        return $pegawai;
    }

    /**
     * Generate username unik dari NIP atau nama.
     */
    private function generateUsername(?string $nip, string $nama): string
    {
        $username = $nip ?: Str::slug($nama) . rand(100, 999);

        while (User::where('username', $username)->exists()) {
            $username .= rand(100, 999);
        }

        return $username;
    }
}