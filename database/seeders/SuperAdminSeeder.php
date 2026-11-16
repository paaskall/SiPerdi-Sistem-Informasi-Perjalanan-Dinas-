<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role Super Admin Ada
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        // 2. Buat User Super Admin
        $user = User::updateOrCreate(
            ['username' => 'superadmin'], // Ganti dengan username yang diinginkan
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@siperdi.local', // Email dummy
                'nip' => '000000000000000000', // NIP dummy
                'password' => Hash::make('password123'), // Password default
                'divisi_id' => null,
                'jabatan_id' => null,
                'golongan_id' => null,
                'pangkat_id' => null,
            ]
        );

        // 3. Assign Role
        $user->assignRole($role);

        $this->command->info('   User Super Admin berhasil dibuat.');
        $this->command->info('   Username: superadmin');
        $this->command->info('   Password: password123');
    }
}