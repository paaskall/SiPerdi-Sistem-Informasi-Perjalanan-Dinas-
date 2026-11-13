<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatLaporan extends Component
{
    use WithPagination;

    public function render()
    {
        $perjalanans = PerjalananDinas::with(['kotaTujuan', 'laporan'])
            ->where('pegawai_id', auth()->id())
            // Tampilkan hanya yang sudah lewat tahap pencetakan (PRINTED, RECEIVED_BY_FINANCE, COMPLETED, PIMPINAN_SIGNED)
            ->whereIn('status', ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.perjalanan.riwayat-laporan', compact('perjalanans'))->layout('layouts.app');
    }
}