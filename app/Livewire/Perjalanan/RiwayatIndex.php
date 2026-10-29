<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatIndex extends Component
{
    use WithPagination;

    public function render()
    {
        // Pegawai hanya melihat pengajuannya sendiri
        $riwayats = PerjalananDinas::with(['kotaTujuan'])
            ->where('pegawai_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.perjalanan.riwayat-index', compact('riwayats'))->layout('layouts.app');
    }
}