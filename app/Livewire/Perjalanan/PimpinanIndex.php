<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use Livewire\Component;
use Livewire\WithPagination;

class PimpinanIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $pengajuans = PerjalananDinas::with(['pegawai', 'kotaTujuan', 'divisi'])
            ->whereIn('status', ['FINANCE_APPROVED', 'PIMPINAN_SIGNED'])
            ->orderBy('finance_approved_at', 'desc')
            ->paginate(10);

        return view('livewire.perjalanan.pimpinan-index', compact('pengajuans'))->layout('layouts.app');
    }

    // 1. Method untuk memunculkan SweetAlert Konfirmasi
    public function konfirmasiTtd($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Tanda Tangani SPPD?',
            'text' => 'Dokumen PDF akan segera diterbitkan dengan tanda tangan Anda.',
            'icon' => 'question',
            'confirmButtonText' => 'Ya, Tanda Tangani',
            'cancelButtonText' => 'Batal',
            'method' => 'prosesTtd', // Method yang akan dipanggil jika user klik "Ya"
            'params' => [$id],       // Parameter (ID) yang dikirim
        ]);
    }

    // 2. Method yang dieksekusi setelah user klik "Ya" di SweetAlert
    public function prosesTtd($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Validasi TTD
        if (!auth()->user()->ttd_path) {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text' => 'Anda belum mengunggah Tanda Tangan di menu Profil!',
                'icon' => 'error',
            ]);
            return;
        }

        $perjalanan->update([
            'status' => 'PIMPINAN_SIGNED',
        ]);

        // Notifikasi Sukses
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'SPPD berhasil ditandatangani. Silakan unduh dokumennya.',
            'icon' => 'success',
        ]);
    }

    public function setujui($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Validasi TTD
        if (!auth()->user()->ttd_path) {
            $this->dispatch('swal', [
                'title' => 'Gagal!',
                'text' => 'Anda belum mengunggah Tanda Tangan di menu Profil!',
                'icon' => 'error',
            ]);
            return;
        }

        $perjalanan->update([
            'status' => 'PIMPINAN_SIGNED',
        ]);

        // Notifikasi Sukses
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'SPPD berhasil ditandatangani.',
            'icon' => 'success',
        ]);
    }
}