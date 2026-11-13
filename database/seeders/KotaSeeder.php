<?php

namespace Database\Seeders;

use App\Models\Kota;
use Illuminate\Database\Seeder;

class KotaSeeder extends Seeder
{
    public function run(): void
    {
        // Struktur: provinsi => [daftar kota/kabupaten]
        // Total: 514 kabupaten/kota
        $data = [
            'Aceh' => [
                'Banda Aceh', 'Sabang', 'Langsa', 'Lhokseumawe', 'Subulussalam',
                'Kabupaten Aceh Besar', 'Kabupaten Pidie', 'Kabupaten Pidie Jaya',
                'Kabupaten Bireuen', 'Kabupaten Aceh Utara', 'Kabupaten Aceh Timur',
                'Kabupaten Aceh Tamiang', 'Kabupaten Gayo Lues', 'Kabupaten Aceh Tenggara',
                'Kabupaten Aceh Singkil', 'Kabupaten Simeulue', 'Kabupaten Aceh Selatan',
                'Kabupaten Aceh Barat Daya', 'Kabupaten Aceh Barat', 'Kabupaten Nagan Raya',
                'Kabupaten Aceh Jaya', 'Kabupaten Bener Meriah', 'Kabupaten Aceh Tengah',
            ],
            'Sumatera Utara' => [
                'Medan', 'Binjai', 'Tebing Tinggi', 'Pematangsiantar', 'Tanjungbalai',
                'Sibolga', 'Padangsidimpuan', 'Gunungsitoli',
                'Kabupaten Deli Serdang', 'Kabupaten Langkat', 'Kabupaten Karo',
                'Kabupaten Simalungun', 'Kabupaten Asahan', 'Kabupaten Labuhanbatu',
                'Kabupaten Labuhanbatu Utara', 'Kabupaten Labuhanbatu Selatan',
                'Kabupaten Tapanuli Utara', 'Kabupaten Tapanuli Tengah',
                'Kabupaten Tapanuli Selatan', 'Kabupaten Mandailing Natal',
                'Kabupaten Padang Lawas', 'Kabupaten Padang Lawas Utara',
                'Kabupaten Nias', 'Kabupaten Nias Utara', 'Kabupaten Nias Barat',
                'Kabupaten Nias Selatan', 'Kabupaten Humbang Hasundutan',
                'Kabupaten Samosir', 'Kabupaten Dairi', 'Kabupaten Pakpak Bharat',
                'Kabupaten Batu Bara', 'Kabupaten Serdang Bedagai',
            ],
            'Sumatera Barat' => [
                'Padang', 'Bukittinggi', 'Payakumbuh', 'Padang Panjang', 'Solok',
                'Sawahlunto', 'Pariaman',
                'Kabupaten Pesisir Selatan', 'Kabupaten Solok', 'Kabupaten Sijunjung',
                'Kabupaten Tanah Datar', 'Kabupaten Padang Pariaman', 'Kabupaten Agam',
                'Kabupaten Lima Puluh Kota', 'Kabupaten Pasaman', 'Kabupaten Pasaman Barat',
                'Kabupaten Kepulauan Mentawai', 'Kabupaten Solok Selatan', 'Kabupaten Dharmasraya',
            ],
            'Riau' => [
                'Pekanbaru', 'Dumai',
                'Kabupaten Kampar', 'Kabupaten Rokan Hulu', 'Kabupaten Rokan Hilir',
                'Kabupaten Siak', 'Kabupaten Indragiri Hilir', 'Kabupaten Indragiri Hulu',
                'Kabupaten Pelalawan', 'Kabupaten Bengkalis', 'Kabupaten Kepulauan Meranti',
                'Kabupaten Kuantan Singingi',
            ],
            'Kepulauan Riau' => [
                'Batam', 'Tanjungpinang',
                'Kabupaten Bintan', 'Kabupaten Karimun', 'Kabupaten Natuna',
                'Kabupaten Lingga', 'Kabupaten Kepulauan Anambas',
            ],
            'Jambi' => [
                'Jambi', 'Sungai Penuh',
                'Kabupaten Kerinci', 'Kabupaten Merangin', 'Kabupaten Sarolangun',
                'Kabupaten Batanghari', 'Kabupaten Muaro Jambi',
                'Kabupaten Tanjung Jabung Timur', 'Kabupaten Tanjung Jabung Barat',
                'Kabupaten Tebo', 'Kabupaten Bungo',
            ],
            'Sumatera Selatan' => [
                'Palembang', 'Prabumulih', 'Pagar Alam', 'Lubuk Linggau',
                'Kabupaten Ogan Komering Ulu', 'Kabupaten Ogan Komering Ilir',
                'Kabupaten Muara Enim', 'Kabupaten Lahat', 'Kabupaten Musi Rawas',
                'Kabupaten Musi Banyuasin', 'Kabupaten Banyuasin',
                'Kabupaten Ogan Komering Ulu Timur', 'Kabupaten Ogan Komering Ulu Selatan',
                'Kabupaten Ogan Ilir', 'Kabupaten Empat Lawang',
                'Kabupaten Penukal Abab Lematang Ilir', 'Kabupaten Musi Rawas Utara',
            ],
            'Bengkulu' => [
                'Bengkulu',
                'Kabupaten Bengkulu Selatan', 'Kabupaten Bengkulu Tengah',
                'Kabupaten Bengkulu Utara', 'Kabupaten Kaur', 'Kabupaten Kepahiang',
                'Kabupaten Lebong', 'Kabupaten Mukomuko', 'Kabupaten Rejang Lebong',
                'Kabupaten Seluma',
            ],
            'Lampung' => [
                'Bandar Lampung', 'Metro',
                'Kabupaten Lampung Selatan', 'Kabupaten Lampung Tengah',
                'Kabupaten Lampung Utara', 'Kabupaten Lampung Barat',
                'Kabupaten Lampung Timur', 'Kabupaten Tanggamus',
                'Kabupaten Tulang Bawang', 'Kabupaten Tulang Bawang Barat',
                'Kabupaten Pesawaran', 'Kabupaten Pringsewu', 'Kabupaten Mesuji',
                'Kabupaten Pesisir Barat', 'Kabupaten Way Kanan',
            ],
            'Bangka Belitung' => [
                'Pangkalpinang',
                'Kabupaten Bangka', 'Kabupaten Bangka Barat', 'Kabupaten Bangka Selatan',
                'Kabupaten Bangka Tengah', 'Kabupaten Belitung', 'Kabupaten Belitung Timur',
            ],
            'DKI Jakarta' => [
                'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat',
                'Jakarta Selatan', 'Jakarta Timur', 'Kabupaten Kepulauan Seribu',
            ],
            'Jawa Barat' => [
                'Bandung', 'Bekasi', 'Bogor', 'Cimahi', 'Cirebon', 'Depok',
                'Sukabumi', 'Tasikmalaya', 'Banjar',
                'Kabupaten Bandung', 'Kabupaten Bandung Barat', 'Kabupaten Bekasi',
                'Kabupaten Bogor', 'Kabupaten Ciamis', 'Kabupaten Cianjur',
                'Kabupaten Cirebon', 'Kabupaten Garut', 'Kabupaten Indramayu',
                'Kabupaten Karawang', 'Kabupaten Kuningan', 'Kabupaten Majalengka',
                'Kabupaten Pangandaran', 'Kabupaten Purwakarta', 'Kabupaten Subang',
                'Kabupaten Sukabumi', 'Kabupaten Sumedang', 'Kabupaten Tasikmalaya',
            ],
            'Jawa Tengah' => [
                'Semarang', 'Surakarta', 'Magelang', 'Salatiga', 'Pekalongan', 'Tegal',
                'Kabupaten Banjarnegara', 'Kabupaten Banyumas', 'Kabupaten Batang',
                'Kabupaten Blora', 'Kabupaten Boyolali', 'Kabupaten Brebes',
                'Kabupaten Cilacap', 'Kabupaten Demak', 'Kabupaten Grobogan',
                'Kabupaten Jepara', 'Kabupaten Karanganyar', 'Kabupaten Kebumen',
                'Kabupaten Kendal', 'Kabupaten Klaten', 'Kabupaten Kudus',
                'Kabupaten Magelang', 'Kabupaten Pati', 'Kabupaten Pekalongan',
                'Kabupaten Pemalang', 'Kabupaten Purbalingga', 'Kabupaten Purworejo',
                'Kabupaten Rembang', 'Kabupaten Semarang', 'Kabupaten Sragen',
                'Kabupaten Sukoharjo', 'Kabupaten Tegal', 'Kabupaten Temanggung',
                'Kabupaten Wonogiri', 'Kabupaten Wonosobo',
            ],
            'DI Yogyakarta' => [
                'Yogyakarta',
                'Kabupaten Bantul', 'Kabupaten Gunungkidul',
                'Kabupaten Kulon Progo', 'Kabupaten Sleman',
            ],
            'Jawa Timur' => [
                'Surabaya', 'Malang', 'Kediri', 'Madiun', 'Mojokerto', 'Pasuruan',
                'Probolinggo', 'Blitar', 'Batu',
                'Kabupaten Bangkalan', 'Kabupaten Banyuwangi', 'Kabupaten Blitar',
                'Kabupaten Bojonegoro', 'Kabupaten Bondowoso', 'Kabupaten Gresik',
                'Kabupaten Jember', 'Kabupaten Jombang', 'Kabupaten Kediri',
                'Kabupaten Lamongan', 'Kabupaten Lumajang', 'Kabupaten Madiun',
                'Kabupaten Magetan', 'Kabupaten Malang', 'Kabupaten Mojokerto',
                'Kabupaten Nganjuk', 'Kabupaten Ngawi', 'Kabupaten Pacitan',
                'Kabupaten Pamekasan', 'Kabupaten Pasuruan', 'Kabupaten Ponorogo',
                'Kabupaten Probolinggo', 'Kabupaten Sampang', 'Kabupaten Sidoarjo',
                'Kabupaten Situbondo', 'Kabupaten Sumenep', 'Kabupaten Trenggalek',
                'Kabupaten Tuban', 'Kabupaten Tulungagung',
            ],
            'Banten' => [
                'Serang', 'Tangerang', 'Tangerang Selatan', 'Cilegon',
                'Kabupaten Lebak', 'Kabupaten Pandeglang',
                'Kabupaten Serang', 'Kabupaten Tangerang',
            ],
            'Bali' => [
                'Denpasar',
                'Kabupaten Badung', 'Kabupaten Bangli', 'Kabupaten Buleleng',
                'Kabupaten Gianyar', 'Kabupaten Jembrana', 'Kabupaten Karangasem',
                'Kabupaten Klungkung', 'Kabupaten Tabanan',
            ],
            'Nusa Tenggara Barat' => [
                'Mataram', 'Bima',
                'Kabupaten Bima', 'Kabupaten Dompu', 'Kabupaten Lombok Barat',
                'Kabupaten Lombok Tengah', 'Kabupaten Lombok Timur',
                'Kabupaten Lombok Utara', 'Kabupaten Sumbawa', 'Kabupaten Sumbawa Barat',
            ],
            'Nusa Tenggara Timur' => [
                'Kupang',
                'Kabupaten Alor', 'Kabupaten Belu', 'Kabupaten Ende',
                'Kabupaten Flores Timur', 'Kabupaten Kupang', 'Kabupaten Lembata',
                'Kabupaten Malaka', 'Kabupaten Manggarai', 'Kabupaten Manggarai Barat',
                'Kabupaten Manggarai Timur', 'Kabupaten Nagekeo', 'Kabupaten Ngada',
                'Kabupaten Rote Ndao', 'Kabupaten Sabu Raijua', 'Kabupaten Sikka',
                'Kabupaten Sumba Barat', 'Kabupaten Sumba Barat Daya',
                'Kabupaten Sumba Tengah', 'Kabupaten Sumba Timur',
                'Kabupaten Timor Tengah Selatan', 'Kabupaten Timor Tengah Utara',
            ],
            'Kalimantan Barat' => [
                'Pontianak', 'Singkawang',
                'Kabupaten Bengkayang', 'Kabupaten Kapuas Hulu',
                'Kabupaten Kayong Utara', 'Kabupaten Ketapang',
                'Kabupaten Kubu Raya', 'Kabupaten Landak', 'Kabupaten Melawi',
                'Kabupaten Mempawah', 'Kabupaten Sambas', 'Kabupaten Sanggau',
                'Kabupaten Sekadau', 'Kabupaten Sintang',
            ],
            'Kalimantan Tengah' => [
                'Palangka Raya',
                'Kabupaten Barito Selatan', 'Kabupaten Barito Timur',
                'Kabupaten Barito Utara', 'Kabupaten Gunung Mas', 'Kabupaten Kapuas',
                'Kabupaten Katingan', 'Kabupaten Kotawaringin Barat',
                'Kabupaten Kotawaringin Timur', 'Kabupaten Lamandau',
                'Kabupaten Murung Raya', 'Kabupaten Pulang Pisau',
                'Kabupaten Seruyan', 'Kabupaten Sukamara',
            ],
            'Kalimantan Selatan' => [
                'Banjarmasin', 'Banjarbaru',
                'Kabupaten Balangan', 'Kabupaten Banjar', 'Kabupaten Barito Kuala',
                'Kabupaten Hulu Sungai Selatan', 'Kabupaten Hulu Sungai Tengah',
                'Kabupaten Hulu Sungai Utara', 'Kabupaten Kotabaru',
                'Kabupaten Tabalong', 'Kabupaten Tanah Bumbu',
                'Kabupaten Tanah Laut', 'Kabupaten Tapin',
            ],
            'Kalimantan Timur' => [
                'Samarinda', 'Balikpapan', 'Bontang',
                'Kabupaten Berau', 'Kabupaten Kutai Barat',
                'Kabupaten Kutai Kartanegara', 'Kabupaten Kutai Timur',
                'Kabupaten Mahakam Ulu', 'Kabupaten Paser',
                'Kabupaten Penajam Paser Utara',
            ],
            'Kalimantan Utara' => [
                'Tarakan',
                'Kabupaten Bulungan', 'Kabupaten Malinau',
                'Kabupaten Nunukan', 'Kabupaten Tana Tidung',
            ],
            'Sulawesi Utara' => [
                'Manado', 'Bitung', 'Tomohon', 'Kotamobagu',
                'Kabupaten Bolaang Mongondow',
                'Kabupaten Bolaang Mongondow Selatan',
                'Kabupaten Bolaang Mongondow Timur',
                'Kabupaten Bolaang Mongondow Utara',
                'Kabupaten Kepulauan Sangihe',
                'Kabupaten Kepulauan Siau Tagulandang Biaro',
                'Kabupaten Kepulauan Talaud', 'Kabupaten Minahasa',
                'Kabupaten Minahasa Selatan', 'Kabupaten Minahasa Tenggara',
                'Kabupaten Minahasa Utara',
            ],
            'Gorontalo' => [
                'Gorontalo',
                'Kabupaten Boalemo', 'Kabupaten Bone Bolango',
                'Kabupaten Gorontalo', 'Kabupaten Gorontalo Utara',
                'Kabupaten Pohuwato',
            ],
            'Sulawesi Tengah' => [
                'Palu',
                'Kabupaten Banggai', 'Kabupaten Banggai Kepulauan',
                'Kabupaten Banggai Laut', 'Kabupaten Buol', 'Kabupaten Donggala',
                'Kabupaten Morowali', 'Kabupaten Morowali Utara',
                'Kabupaten Parigi Moutong', 'Kabupaten Poso', 'Kabupaten Sigi',
                'Kabupaten Tojo Una-Una', 'Kabupaten Tolitoli',
            ],
            'Sulawesi Selatan' => [
                'Makassar', 'Palopo', 'Parepare',
                'Kabupaten Bantaeng', 'Kabupaten Barru', 'Kabupaten Bone',
                'Kabupaten Bulukumba', 'Kabupaten Enrekang', 'Kabupaten Gowa',
                'Kabupaten Jeneponto', 'Kabupaten Kepulauan Selayar',
                'Kabupaten Luwu', 'Kabupaten Luwu Timur', 'Kabupaten Luwu Utara',
                'Kabupaten Maros', 'Kabupaten Pangkajene dan Kepulauan',
                'Kabupaten Pinrang', 'Kabupaten Sidenreng Rappang',
                'Kabupaten Sinjai', 'Kabupaten Soppeng', 'Kabupaten Takalar',
                'Kabupaten Tana Toraja', 'Kabupaten Toraja Utara', 'Kabupaten Wajo',
            ],
            'Sulawesi Tenggara' => [
                'Kendari', 'Baubau',
                'Kabupaten Bombana', 'Kabupaten Buton',
                'Kabupaten Buton Selatan', 'Kabupaten Buton Tengah',
                'Kabupaten Buton Utara', 'Kabupaten Kolaka',
                'Kabupaten Kolaka Timur', 'Kabupaten Kolaka Utara',
                'Kabupaten Konawe', 'Kabupaten Konawe Kepulauan',
                'Kabupaten Konawe Selatan', 'Kabupaten Konawe Utara',
                'Kabupaten Muna', 'Kabupaten Muna Barat', 'Kabupaten Wakatobi',
            ],
            'Sulawesi Barat' => [
                'Mamuju',
                'Kabupaten Majene', 'Kabupaten Mamasa',
                'Kabupaten Mamuju', 'Kabupaten Mamuju Tengah',
                'Kabupaten Pasangkayu',
            ],
            'Maluku' => [
                'Ambon', 'Tual',
                'Kabupaten Buru', 'Kabupaten Buru Selatan',
                'Kabupaten Kepulauan Aru', 'Kabupaten Maluku Barat Daya',
                'Kabupaten Maluku Tengah', 'Kabupaten Maluku Tenggara',
                'Kabupaten Maluku Tenggara Barat',
                'Kabupaten Seram Bagian Barat', 'Kabupaten Seram Bagian Timur',
            ],
            'Maluku Utara' => [
                'Ternate', 'Tidore Kepulauan',
                'Kabupaten Halmahera Barat', 'Kabupaten Halmahera Tengah',
                'Kabupaten Halmahera Timur', 'Kabupaten Halmahera Selatan',
                'Kabupaten Halmahera Utara', 'Kabupaten Kepulauan Sula',
                'Kabupaten Pulau Morotai', 'Kabupaten Pulau Taliabu',
            ],
            'Papua' => [
                'Jayapura',
                'Kabupaten Biak Numfor', 'Kabupaten Jayapura',
                'Kabupaten Keerom', 'Kabupaten Kepulauan Yapen',
                'Kabupaten Mamberamo Raya', 'Kabupaten Sarmi',
                'Kabupaten Supiori', 'Kabupaten Waropen',
            ],
            'Papua Barat' => [
                'Sorong',
                'Kabupaten Fakfak', 'Kabupaten Kaimana',
                'Kabupaten Manokwari', 'Kabupaten Manokwari Selatan',
                'Kabupaten Pegunungan Arfak', 'Kabupaten Teluk Bintuni',
                'Kabupaten Teluk Wondama',
            ],
            'Papua Selatan' => [
                'Kabupaten Asmat', 'Kabupaten Boven Digoel',
                'Kabupaten Mappi', 'Kabupaten Merauke',
            ],
            'Papua Tengah' => [
                'Kabupaten Deiyai', 'Kabupaten Dogiyai',
                'Kabupaten Mimika', 'Kabupaten Nabire',
                'Kabupaten Paniai', 'Kabupaten Puncak',
                'Kabupaten Puncak Jaya', 'Kabupaten Intan Jaya',
            ],
            'Papua Pegunungan' => [
                'Kabupaten Jayawijaya', 'Kabupaten Lanny Jaya',
                'Kabupaten Mamberamo Tengah', 'Kabupaten Nduga',
                'Kabupaten Pegunungan Bintang', 'Kabupaten Tolikara',
                'Kabupaten Yahukimo', 'Kabupaten Yalimo',
            ],
            'Papua Barat Daya' => [
                'Kabupaten Maybrat', 'Kabupaten Raja Ampat',
                'Kabupaten Sorong', 'Kabupaten Sorong Selatan',
                'Kabupaten Tambrauw',
            ],
        ];

        $total = 0;

        foreach ($data as $provinsi => $daftarKota) {
            foreach ($daftarKota as $nama) {
                // firstOrCreate: tidak duplikat kalau dijalankan ulang
                Kota::firstOrCreate(
                    ['nama' => $nama],
                    ['provinsi' => $provinsi]
                );
                $total++;
            }
        }

        $this->command->info("✅ {$total} kota/kabupaten berhasil di-seed (tanpa duplikat).");
    }
}