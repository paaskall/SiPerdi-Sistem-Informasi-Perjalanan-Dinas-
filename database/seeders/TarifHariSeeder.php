<?php

namespace Database\Seeders;

use App\Models\Kota;
use App\Models\Golongan;
use App\Models\TarifHari;
use Illuminate\Database\Seeder;

class TarifHariSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil SEMUA Golongan
        $golongans = Golongan::all();
        if ($golongans->isEmpty()) {
            $this->command->error('Tabel Golongan kosong!');
            return;
        }

        // 2. Ambil SEMUA Kota (514 Kota)
        $kotas = Kota::all();
        if ($kotas->isEmpty()) {
            $this->command->error('Tabel Kota kosong!');
            return;
        }

        $this->command->info("Memproses " . $kotas->count() . " kota dan " . $golongans->count() . " golongan...");

        $totalInserted = 0;

        // 3. Loping semua Kota
        foreach ($kotas as $kota) {
            // Tentukan biaya tambahan (Indeks Kemahalan) berdasarkan provinsi
            $tambahanWilayah = 0;
            $provinsi = strtolower($kota->provinsi ?? '');

            if (str_contains($provinsi, 'jakarta') || str_contains($provinsi, 'papua') || str_contains($provinsi, 'kalimantan timur') || str_contains($provinsi, 'bali')) {
                $tambahanWilayah = 100000; // Wilayah mahal
            } elseif (str_contains($provinsi, 'sulawesi') || str_contains($provinsi, 'sumatera') || str_contains($provinsi, 'jawa')) {
                $tambahanWilayah = 50000; // Wilayah menengah
            } else {
                $tambahanWilayah = 0; // Wilayah standar
            }

            // 4. Looping semua Golongan di setiap Kota
            foreach ($golongans as $golongan) {
                
                // Tentukan Harga Dasar berdasarkan Golongan
                $namaGol = strtolower($golongan->nama);
                $hargaDasar = 0;

                if (str_contains($namaGol, 'iv') || str_contains($namaGol, 'iii')) {
                    $hargaDasar = 450000; // Golongan tinggi
                } elseif (str_contains($namaGol, 'ii')) {
                    $hargaDasar = 380000; // Golongan menengah
                } else {
                    $hargaDasar = 320000; // Golongan rendah
                }

                // Total Nominal = Harga Dasar + Tambahan Wilayah
                $nominal = $hargaDasar + $tambahanWilayah;

                // Simpan ke Database (Aman dari duplikat)
                TarifHari::updateOrCreate(
                    [
                        'kota_id' => $kota->id,
                        'golongan_id' => $golongan->id,
                    ],
                    [
                        'nominal' => $nominal
                    ]
                );

                $totalInserted++;
            }
        }

        $this->command->info("✅ BERHASIL! Total {$totalInserted} data Tarif Harian telah dibuat untuk seluruh kota di Indonesia.");
    }
}