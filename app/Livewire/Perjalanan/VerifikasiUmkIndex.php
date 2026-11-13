<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use Livewire\Component;
use Livewire\WithPagination;

class VerifikasiUmkIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedUmk = null;
    public $isModalOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $umks = PerjalananDinas::with([
                'pegawai.divisi', 'pegawai.golongan', 'pegawai.jabatan', 'pegawai.pangkat',
                'kotaTujuan', 'rincianBiaya.coa', 'penerimaKeuangan'
            ])
            ->whereIn('status', ['PRINTED', 'RECEIVED_BY_FINANCE'])
            ->where(function ($q) {
                $q->where('nomor_pengajuan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('pegawai', function ($q2) {
                      $q2->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->orderByRaw("FIELD(status, 'PRINTED', 'RECEIVED_BY_FINANCE')")
            ->orderBy('printed_at', 'asc')
            ->paginate(10);

        return view('livewire.perjalanan.verifikasi-umk-index', compact('umks'))->layout('layouts.app');
    }

    public function lihat($id)
    {
        $this->selectedUmk = PerjalananDinas::with([
            'pegawai.divisi', 'pegawai.golongan', 'pegawai.jabatan', 'pegawai.pangkat',
            'kotaTujuan', 'rincianBiaya.coa'
        ])->findOrFail($id);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedUmk = null;
    }

    public function terima($id)
    {
        $umk = PerjalananDinas::findOrFail($id);
        $umk->update([
            'status' => 'RECEIVED_BY_FINANCE',
            'received_by_finance_at' => now(),
            'received_by_finance_id' => auth()->id(),
        ]);

        activity()
            ->performedOn($umk)
            ->causedBy(auth()->user())
            ->withProperties(['nomor_pengajuan' => $umk->nomor_pengajuan])
            ->log('Menerima & memverifikasi form UMK');

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Form UMK telah diverifikasi.',
            'icon' => 'success',
        ]);

        $this->closeModal();
    }
}