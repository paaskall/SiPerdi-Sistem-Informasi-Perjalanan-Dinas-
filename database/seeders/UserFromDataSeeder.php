<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Pangkat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserFromDataSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan divisi & role ada
        $defaultDivisi = Divisi::firstOrCreate(
            ['kode' => 'PUSLATBANG'],
            ['nama' => 'Puslatbang KMP']
        );

        $rolePegawai = Role::firstOrCreate(['name' => 'pegawai', 'guard_name' => 'web']);

        // Data pegawai (format: [nama, nip, pangkat_gol, jabatan])
        $data = [
            ['Drs. Ibrahim Hamdani, M.Si', '19500622 197903 1 001', 'Pembina Utama- IV/e', 'Widyaiswara Utama'],
            ['Dr. Muhammad Firdaus, MBA', '19660303 199402 1 001', 'Pembina Utama Muda-IV/c', 'Kepala Pusat'],
            ['Ir. H. A. Bachtiar Arief, MTP', '19590325 198903 1 005', 'Pembina Utama Muda-IV/c', 'Widyaiswara Madya'],
            ['dr. Grace V Dumalang, M.Kes', '19590227 198901 1 001', 'Pembina Utama Muda-IV/c', 'Widyaiswara Utama'],
            ['Dr. Yulianus Tandi, SH, M.Si', '19540101 198401 1 001', 'Pembina Utama Muda-IV/d', 'Widyaiswara Utama'],
            ['DR. Muhammad Aswad, M.Si', '19670206 199303 1 001', 'Pembina Tk. I- IV/c', 'Kepala Pusat Pusbangkom Pimnas'],
            ['Dr. Sulaiman Fattah, M.Si', '19670601 199402 1 002', 'Pembina Tk. I- IV/b', 'Kepala Bidang Kajian Manajemen Pemerintahan'],
            ['Dr. Lukman, M.Si', '19680803 199402 1 002', 'Pembina-IV/a', 'Kepala Bidang Kajian Kebijakan dan Inovasi Administrasi Negara'],
            ['Drs. Kaharuddin, M.Pd', '19630512 198803 1 001', 'Pembina-IV/a', 'Pengelola Kajian dan Inovasi Kebijakan Administrasi Negara'],
            ['Yohanis Sulle,S.Sos', '19580414 198803 1 001', 'Pembina-IV/a', 'Pustakawan Madya'],
            ['Nurwahyudianti, S.S, M.HRM.IR', '19720919 198803 2 001', 'Penata Tk.I-III/d', 'Widyaiswara Madya'],
            ['Ramli, S.Sos.,M.Si', '19641231 198603 1 001', 'Pembina-IV/a', 'Kepala Bagian Administrasi'],
            ['Suyono,S.Kom, MM', '19640318 198903 1 001', 'Pembina-IV/a', 'Widyaiswara Madya'],
            ['Firdaus Hafid, SS,M.LMEd', '19721122 199902 1 001', 'Pembina-IV/a', 'Widyaiswara Madya'],
            ['Hidayaturahmi, S.Sos,MPA', '19750202 200002 2 001', 'Penata Tk.I-III/d', 'Analis Kepegawaian Pertama'],
            ['Nirwati Yapardy, S.Pd.,M.HRM.IR', '19720223 199803 2 001', 'Penata Tk. I-III/d', 'Widyaiswara Muda'],
            ['Andi Salviah, S.IP,MM', '19741008 200804 2 001', 'Penata Tk. I-III/d', 'Widyaiswara Muda'],
            ['Muhammad Yunus, S.IP.,S.Psi., M.Si', '19780721 200804 1 001', 'Penata-III/c', 'Widyaiswara Muda'],
            ['Dewi Ratna, S.Sos, MAP', '19690711 198903 2 001', 'Penata, III/c', 'Pranata Humas Muda'],
            ['Muskamal, S.Sos.,M.Si', '19820621 200804 1 001', 'Penata-III/c', 'Peneliti Muda'],
            ['Anita, S.Sos, M.Si', '19821015 200804 2 002', 'Penata -III/c', 'Peneliti Muda'],
            ['Sudarmi Narwis, SE., MM', '19701030 200501 2 001', 'Penata, III/c', 'Kepala Sub Bagian Keuangan'],
            ['Nurati Rajab, SS,MM', '19731110 200501 2 001', 'Penata-III/c', 'Kasubag Perencanaan dan Evaluasi Program'],
            ['Dr. Johann Tarru, M.Si', '19770103 200912 1 001', 'Penata Muda Tk.I-III/b', 'Widyaiswara Pertama'],
            ['Zulchaidir, S.Sos, MPA', '19830115 200912 1 003', 'Penata Tk.I-III/d', 'Koordinator Pelatihan dan Pengembangan'],
            ['Yulianto, S,.Sos', '19650708 198903 1 019', 'Penata Muda Tk.I-III/b', 'Widyaiswara Pertama'],
            ['Murniati', '19610721 198603 2 002', 'Penata Muda Tk.I-III/b', 'Pemroses Administrasi Kepegawaian'],
            ['Ahmad Sukarno Syahrir, S.IP, M.Adm,SDA', '19800424 200804 1 002', 'Penata Muda Tk.I-III/b', 'Peneliti Pertama'],
            ['Ayun Sri Damayanti, SH, MH', '19760324 201101 2 002', 'Penata Muda Tk.I- III/b', 'Peneliti Muda'],
            ['Edi Abdullah, SH, MH', '19821110 201101 1 007', 'Penata Muda Tk.I- III/b', 'Pengelola Naskah Hukum'],
            ['Maylitha Achmad, S.Psi, MBA', '19840522 201101 2 011', 'Penata Muda Tk.I- III/b', 'Widyaiswara Pertama'],
            ['Milawaty,SS, MM', '19760507 201101 2 003', 'Penata Muda Tk.I- III/d', 'Peneliti Ahli Muda'],
            ['Syakib Arsalam, S.HI,LL.M', '19840221 201101 1 010', 'Penata Muda Tk.I- III/b', 'Peneliti Ahli Muda'],
            ['Zaenal Z, S.Sos, M.Adm.Pemb', '19840415 200804 1 002', 'Penata, III/c', 'Peneliti Muda'],
            ['Hamzah, S.Sos', '19641231 198503 1 002', 'Penata Muda-III/a', 'Pengelola Kerumahtanggaan Urusan Pemeliharaan Gedung, Halaman Kantor dan Taman'],
            ['Irmayani, S.AP', '19750605 200212 2 001', 'Penata Muda, III/a', 'Pranata Humas Pertama'],
            ['Muhammad Iqbal, S.Sos', '19851014 200912 1 002', 'Penata Muda-III/a', 'Pranata Humas Pertama'],
            ['Muh. Arifin Nuru, S.Sos', '19580821 198503 1 001', 'Penata Muda-III/a', 'Pengelola Kajian dan Inovasi Kebijakan Administrasi Negara'],
            ['Nur Masyita, S.Psi', '19810315 201101 2 007', 'Penata Muda- III/a', 'Pemroses Administrasi SDM'],
            ['Suharna Sudirman, S.Kom', '19810303 200902 2 006', 'Penata - III/c', 'Pemroses Perencanaan dan Program'],
            ['Nurlaela, A.Mk', '19841212 200804 2 001', 'Penata Muda Tk.I- III/b', 'Perawat Pelaksana'],
            ['Hasbullah, A.Md', '19730218 200604 1 002', 'Pengatur Tk.I-II/d', 'Pranata Komputer Pelaksana'],
            ['Nurmila, A.Md', '19800726 200604 2 002', 'Pengatur Tk.I-II/d', 'Pengelola Keuangan Unit'],
            ['Erdiansyah, A.Md', '19800730 200804 1 001', 'Pengatur Tk.I-II/d', 'Pustakawan Pelaksana dan Pengelola Informasi SIDA'],
            ['Carmelia, A.Md', '19811011 200804 2 001', 'Pengatur Tk.I-II/d', 'Arsiparis Pelaksana dan Evaluator Diklat'],
            ['Ahmad Amiruddin, A.Mk', '19860203 200804 1 001', 'Pengatur Tk.I-II/d', 'Perawat Pelaksana dan Pengelola Kepesertaan dan Alumni'],
            ['St. Aflaha, A.Md', '19801031 200804 2 001', 'Pengatur Tk.I-II/d', 'Pustakawan Pelaksana'],
            ['Edi Saputra, S.Kom', '19860318 201101 1 006', 'Penata Muda-III/a', 'Pengelola Pengadaan Barang dan Jasa Pertama'],
            ['M. Yusuf', '19580205 198010 1 002', 'Pengatur Muda Tk.I- II/b', 'Pengelola Kerumahtanggaan Urusan Perjadin dan Kendaraan'],
            ['T.E. Bimo Tunggul, SAP', '19800821 200501 1 001', 'Penata Muda, III/a', 'Pengelola Pengadaan Barang dan Jasa Pertama'],
            ['Erman Fahruddin, S.Si', '19771016 200501 1 001', 'Penata Muda Tk.I- III/b', 'Staf Pengelola Kajian Menejemen Pemerintahan'],
            ['Hariyanto', '19810307 200501 1 001', 'Pengatur Muda Tk.I- II/b', 'Pemroses Administrasi SDM'],
            ['Erniaty Dahlan', '19770327 200501 2 001', 'Penata Muda, III/a', 'Arsiparis'],
            ['Fitriah', '19850214 200604 2 007', 'Pengatur Muda Tk.I -II/b', 'Bendahara Pengeluaran'],
            ['Lionard Untung Niman', '19700415 200604 1 007', 'Pengatur - II/c', 'Pemroses Pelaporan'],
            ['Marwan', '19720720 200604 1 007', 'Pengatur Tk.I-II/d', 'Pengelola Penyelenggara Diklat'],
            ['Rizal', '19730929 200604 1 004', 'Pengatur Tk.I-II/d', 'Pengelola Perpustakaan'],
            ['Sri Andari', '19751231 200604 2 019', 'Pengatur - II/c', 'Bendahara Penerima'],
            ['Juma Ali', '19630317 200604 1 010', 'Pengatur Tk.I-II/d', 'Pengelola Penyelenggara Diklat'],
            ['Asriani Rasyid, SE.Ak', '19770902 200701 2 001', 'Penata Muda, III/a', 'Pengelola Kajian Kebijakan dan Inovasi Administrasi Negara'],
            ['Jamal', '19831223 200902 1 004', 'Pengatur Muda Tk.I-II/b', 'Pembuat Daftar Gaji Pegawai'],
            ['Munawar', '19820118 200902 1 006', 'Pengatur Muda -II/a', 'Pengelola BMN'],
            ['Haris', '19800818 200902 1 004', 'Pengatur Muda Tk.I-II/b', 'Pengelola Perpustakaan'],
            ['Andi Irsan Ismunandar, ST', '19870329 201403 1 000', 'Penata Muda, III/a', 'Pengadministrasi Umum Bidang Diklat'],
            ['Irwan Anas, S.Sos', '19861205 201403 1 002', 'Penata Muda, III/a', 'Pengelola Keuangan Unit'],
            ['Satria Eka Tri Laksana, S.IP', '19920421 201403 1 000', 'Penata Muda, III/a', 'Analis Kebijakan'],
            ['Mutmainnah, S.IP', '19900324 201403 2 000', 'Penata Muda, III/a', 'Evaluator Diklat'],
            ['Nur Khasanah Latief, S.IP', '19920422 201801 2 002', 'Penata Muda, III/a', 'Pengelola Data Kajian Manajemen Pemerintahan'],
            ['Taufik, SE', '19840307 201801 1 001', 'Penata Muda, III/a', 'Pengelola Keuangan'],
            ['Andi Dirga Putra, S.IP', '19890421 201801 1 002', 'Penata Muda, III/a', 'Sekretaris Kepala Pusat'],
            ['Andi Wahyudin, SE., M.Si', '19841125 201801 1 001', 'Penata Muda, III/b', 'Pengelola Diklat'],
            ['Anisa Mifrohatun Fathiyah, SIA', '19950122 201801 2 001', 'Penata Muda, III/a', 'Pengelola Layanan Kehumasan'],
            ['Avrina Dwijayanti, S.IP', '19900405 201801 2 002', 'Penata Muda, III/a', 'Sekretaris Kepala Pusat'],
            ['Fajar Lingga Prasetya, SAB', '19850603 201801 1 001', 'Penata Muda, III/a', 'Pengelola SDM'],
            ['Fenty Kusuma Wardani, S.Psi', '19930622 201801 2 004', 'Penata Muda, III/a', 'Pengelola Diklat'],
            ['Lutfina Thalita Ericha Zainsa, SH', '19940929 201801 2 003', 'Penata Muda, III/a', 'Pengelola Diklat'],
            ['Rafika, S.ST', '19940618 201801 2 001', 'Penata Muda, III/a', 'Pengelola Gedung'],
            ['Wahyuni Fajaruddin, SH., MH', '19891111 201801 2 001', 'Penata Muda, III/b', 'Pengelola Diklat'],
            ['Widianto, ST., MM', '19831209 201801 1 001', 'Penata Muda, III/b', 'Widyaiswara Ahli Pertama'],
            ['dr.Rachmawati', '19860507 201902 2 006', 'Penata Muda Tk.I-III/b', 'Dokter Umum'],
            ['Yosafat, A.Md', '19950412 201902 1 005', 'Pengatur - II/c', 'Pranata Surat'],
            ['Ester Situmorang, A.Md', '19951214 201902 2 012', 'Pengatur - II/c', 'Pengelola Layanan Kehumasan'],
        ];

        // Data dengan jabatan tidak lengkap (kosong atau sopir)
        $dataTambahan = [
            ['Dr. Muh. Idris, M.Si', '19641103 199304 1 001', 'Pembina Utama Muda-IV/c', 'Deputi Bidang Diklat Aparatur'],
            ['Dr. Mariman Darto, M.Si', '19720207 200501 1 001', 'Pembina Tk.I-IV/b', 'Kepala PKP2A III LAN Samarinda'],
            ['Dr. Adi Suryanto, M.Si', '196912081994031001', 'Pembina Utama-IV/e', 'Kepala LAN'],
            ['Dra. Reni Suzana, M.P.P.M', '19680831 199401 2 001', 'Pembina Utama Madya-IV/d', 'Sekretaris Utama'],
            ['Dr. Tri Widodo Wahyu Utomo, SH.,MA', '19680715 199401 1 001', 'Pembina Utama-IV/e', 'Deputi Bidang Kajian Kebijakan dan Inovasi Administrasi Negara'],
            ['Dr. Agus Sudrajat, S.Sos.,M.A', '19670804 199003 1 001', 'Pembina Utama Muda-IV/c', 'Deputi Bidang Kajian dan Inovasi Manajemen Aparatur Sipil Negara'],
            ['Dr. Muhammad Taufiq, DEA', '19671117 199401 1 001', 'Pembina Utama Madya-IV/d', 'Deputi Bidang Kebijakan Pengembangan Kompetensi Sipil Negara'],
            ['Dr. Andi Taufik, M.Si', '196807051994021003', 'Pembina Utama Madya-IV/d', 'Kepala Pusat Puslatbang KMP'],
            ['Prof.DR.Ismail Said, SH.MH', '195506121986031001', 'Pembina Utama- IV/e', 'Dosen STIA LAN Jakarta'],
            ['Dr. Makhdum Priyatno, MA', '196108111986031001', 'Pembina Utama- IV/e', 'Ketua STIA LAN Jakarta'],
            ['Drs. Erwin Chara Rusmana, SH.,M.Hum', '64070694', 'Pembina Utama Muda-IV/c', 'Kesatuan Mabes Polri'],
            ['Oskar Janata Agasi Al Hilal, S.A.P.', '19930808 201801 1 002', 'Penata Muda-III/a', 'Petugas Protokol (Kepala LAN)'],
            ['Ali Budi Nugroho, A.Md', '19790525 201502 1 001', 'Pengatur-II/c', 'Bendahara Gaji'],
            ['Firdaus Suharta, S.Pd', '19901106 201502 1 002', 'Penata Muda-III/a', 'Pengadministrasi Umum Bidang Diklat'],
            ['Ramadhinie Lydiasari, SE', '19810704 201502 2 001', 'Penata Muda-III/a', 'Pengelola Kajian'],
        ];

        $allData = array_merge($data, $dataTambahan);
        $count = 0;

        foreach ($allData as $d) {
            [$nama, $nip, $pangkatGol, $jabatanNama] = $d;

            // Bersihkan NIP
            $nipBersih = str_replace(' ', '', $nip);

            // Pisahkan pangkat & golongan (misal "Pembina Utama- IV/e" → "Pembina Utama" & "IV/e")
            $pangkatStr = $pangkatGol;
            $golonganStr = null;
            if (preg_match('/^(.*?)[\s\-]*([IVX]+\/[a-e])$/i', $pangkatGol, $m)) {
                $pangkatStr = trim($m[1]);
                $golonganStr = trim($m[2]);
            }

            // Cari / buat data master
            $pangkat = Pangkat::firstOrCreate(
                ['nama' => $pangkatStr],
                ['golongan_ruang' => $golonganStr]
            );

            $golongan = $golonganStr
                ? Golongan::firstOrCreate(['nama' => 'Golongan ' . explode('/', $golonganStr)[0]])
                : null;

            $jabatan = Jabatan::firstOrCreate(
                ['nama' => $jabatanNama ?: 'Staf'],
                ['level' => 1]
            );

            // Buat user
            $user = User::firstOrCreate(
                ['nip' => $nipBersih],
                [
                    'name' => $nama,
                    'username' => $nipBersih,
                    'email' => $nipBersih . '@siperdi.local',
                    'password' => Hash::make('password'),
                    'divisi_id' => $defaultDivisi->id,
                    'jabatan_id' => $jabatan->id,
                    'pangkat_id' => $pangkat->id,
                    'golongan_id' => $golongan?->id,
                    'auth_provider' => 'local',
                    'aktif' => true,
                ]
            );

            // Assign role jika belum punya
            if (!$user->hasRole('pegawai')) {
                $user->assignRole($rolePegawai);
            }

            $count++;
        }

        $this->command->info("Berhasil membuat {$count} user pegawai.");
    }
}