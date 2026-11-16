<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PerjalananDinas;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $firstName = explode(' ', trim($user->name))[0];
        $roles = $user->getRoleNames();

        // ==========================================
        // AKSES CEPAT PER ROLE
        // ==========================================
        $actions = [];

        if ($user->hasRole('pegawai')) {
            $actions[] = ['label' => 'Buat UMK Baru', 'desc' => 'Ajukan Uang Muka Kerja untuk perjalanan dinas baru.', 'route' => 'perjalanan.buat', 'icon' => 'M12 5v14M5 12h14', 'color' => '#1e3a8a'];
            $actions[] = ['label' => 'Riwayat UMK', 'desc' => 'Pantau status UMK dan cetak formulir UMK Anda.', 'route' => 'perjalanan.riwayat', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => '#059669'];
            $actions[] = ['label' => 'Laporan Saya', 'desc' => 'Isi dan pantau laporan hasil perjalanan dinas Anda.', 'route' => 'laporan.riwayat', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => '#d97706'];
        }

        if ($user->hasRole('keuangan')) {
            $actions[] = ['label' => 'Master Data', 'desc' => 'Kelola Divisi, Golongan, COA, Kota, dan Tarif.', 'route' => 'master.divisi', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'color' => '#1e3a8a'];
            $actions[] = ['label' => 'Audit Trail', 'desc' => 'Lihat jejak audit semua aktivitas di sistem.', 'route' => 'audit.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => '#334155'];
        }

        if ($user->hasRole('pimpinan')) {
            $actions[] = ['label' => 'Audit Trail', 'desc' => 'Pantau semua aktivitas yang terjadi di sistem.', 'route' => 'audit.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => '#334155'];
        }

        if ($isSuperAdmin) {
            $actions[] = ['label' => 'Buat UMK', 'desc' => 'Buat UMK untuk pegawai mana pun.', 'route' => 'perjalanan.buat', 'icon' => 'M12 5v14M5 12h14', 'color' => '#1e3a8a'];
            $actions[] = ['label' => 'Semua Riwayat UMK', 'desc' => 'Pantau seluruh UMK dari semua pegawai.', 'route' => 'perjalanan.riwayat', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => '#059669'];
            $actions[] = ['label' => 'Manajemen User', 'desc' => 'Kelola akun pengguna aplikasi.', 'route' => 'master.users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => '#7c3aed'];
            $actions[] = ['label' => 'Master Data', 'desc' => 'Kelola Divisi, Golongan, COA, Kota, dan Tarif.', 'route' => 'master.divisi', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'color' => '#1e3a8a'];
        }

        // ==========================================
        // RINGKASAN UMK
        // ==========================================
        $totalUmk = 0;
        $belumLapor = 0;
        $sudahLapor = 0;

        if ($user->hasRole('pegawai') || $isSuperAdmin) {
            $perjalananUser = PerjalananDinas::with('laporan')
                ->when(!$isSuperAdmin, fn($q) => $q->where('pegawai_id', $user->id))
                ->whereIn('status', ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED'])
                ->get();
            $totalUmk = $perjalananUser->count();
            $belumLapor = $perjalananUser->filter(fn($p) => !$p->laporan)->count();
            $sudahLapor = $perjalananUser->filter(fn($p) => $p->laporan)->count();
        }

        // ==========================================
        // ALUR PERJALANAN
        // ==========================================
        $steps = [
            ['n' => 1, 'title' => 'Isi Form UMK',        'desc' => 'Pegawai melengkapi data perjalanan dan estimasi biaya.', 'roles' => ['pegawai']],
            ['n' => 2, 'title' => 'Cetak & Serahkan',    'desc' => 'Cetak PDF UMK, bawa ke Keuangan untuk diproses.', 'roles' => ['pegawai']],
            ['n' => 3, 'title' => 'Laporkan Hasil',      'desc' => 'Setelah perjalanan, isi laporan hasil dan unggah bukti.', 'roles' => ['pegawai']],
            ['n' => 4, 'title' => 'Selesai',             'desc' => 'Laporan tersimpan dan siap diarsipkan oleh Keuangan.', 'roles' => ['keuangan']],
        ];

        return view('livewire.dashboard', compact(
            'firstName', 'roles', 'actions', 'totalUmk', 'belumLapor', 'sudahLapor', 'steps', 'isSuperAdmin'
        ))->layout($isSuperAdmin ? 'layouts.super-admin' : 'layouts.app');
    }
}