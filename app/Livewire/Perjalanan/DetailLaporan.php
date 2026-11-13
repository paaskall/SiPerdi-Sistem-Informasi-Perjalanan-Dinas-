<?php

namespace App\Livewire\Perjalanan;

use App\Models\LaporanPerjalanan;
use Livewire\Component;

class DetailLaporan extends Component
{
    public $laporan;

    public function mount($id)
    {
        $this->laporan = LaporanPerjalanan::with([
            'perjalanan.pegawai.divisi',
            'perjalanan.pegawai.jabatan',
            'perjalanan.pegawai.golongan',
            'perjalanan.pegawai.pangkat',
            'perjalanan.kotaTujuan',
        ])->findOrFail($id);

        // Guard: pegawai hanya bisa lihat laporannya sendiri
        if (auth()->user()->hasRole('pegawai') && $this->laporan->pegawai_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengakses laporan ini.');
        }
    }

    public function render()
    {
        return view('livewire.perjalanan.detail-laporan')->layout('layouts.app');
    }
}