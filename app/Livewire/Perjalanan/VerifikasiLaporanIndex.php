<?php

namespace App\Livewire\Perjalanan;

use App\Models\LaporanPerjalanan;
use Livewire\Component;
use Livewire\WithPagination;

class VerifikasiLaporanIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedLaporan = null;
    public $isModalOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $laporans = LaporanPerjalanan::with(['perjalanan.pegawai.divisi', 'perjalanan.kotaTujuan'])
            ->where(function ($q) {
                $q->whereHas('perjalanan', function ($q2) {
                    $q2->where('nomor_pengajuan', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('perjalanan.pegawai', function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByRaw("FIELD(status, 'SUBMITTED', 'VERIFIED', 'REJECTED')")
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('livewire.perjalanan.verifikasi-laporan-index', compact('laporans'))->layout('layouts.app');
    }

    public function lihat($id)
    {
        $this->selectedLaporan = LaporanPerjalanan::with([
            'perjalanan.pegawai.divisi', 'perjalanan.pegawai.golongan', 'perjalanan.kotaTujuan'
        ])->findOrFail($id);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedLaporan = null;
    }

    public function verifikasi($id)
    {
        $laporan = LaporanPerjalanan::findOrFail($id);
        $laporan->update(['status' => 'VERIFIED']);

        activity()
            ->performedOn($laporan)
            ->causedBy(auth()->user())
            ->withProperties(['nomor_umk' => $laporan->perjalanan->nomor_pengajuan])
            ->log('Memverifikasi laporan hasil perjalanan dinas');

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Laporan hasil telah diverifikasi.',
            'icon' => 'success',
        ]);

        $this->closeModal();
    }

    public function tolak($id)
    {
        $laporan = LaporanPerjalanan::findOrFail($id);
        $laporan->update(['status' => 'REJECTED']);

        activity()
            ->performedOn($laporan)
            ->causedBy(auth()->user())
            ->withProperties(['nomor_umk' => $laporan->perjalanan->nomor_pengajuan])
            ->log('Menolak laporan hasil perjalanan dinas');

        $this->dispatch('swal', [
            'title' => 'Laporan Ditolak',
            'text' => 'Laporan hasil telah ditolak. Pegawai dapat merevisi.',
            'icon' => 'warning',
        ]);

        $this->closeModal();
    }
}