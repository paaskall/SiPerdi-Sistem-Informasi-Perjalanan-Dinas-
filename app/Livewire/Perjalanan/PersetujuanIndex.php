<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use Livewire\Component;
use Livewire\WithPagination;

class PersetujuanIndex extends Component
{
    use WithPagination;

    public $perjalananId, $catatan_keuangan;
    public $isModalOpen = false;

    public function render()
    {
        $pengajuans = PerjalananDinas::with(['pegawai', 'kotaTujuan', 'divisi'])
            ->where('status', 'SUBMITTED')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('livewire.perjalanan.persetujuan-index', compact('pengajuans'))->layout('layouts.app');
    }

    public function openModal($id)
    {
        $this->resetValidation();
        $this->perjalananId = $id;
        $this->catatan_keuangan = '';
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function setujui()
    {
        $perjalanan = PerjalananDinas::findOrFail($this->perjalananId);
        $perjalanan->update([
            'status' => 'FINANCE_APPROVED',
            'finance_approved_by' => auth()->id(),
            'finance_approved_at' => now(),
            'catatan_keuangan' => $this->catatan_keuangan,
        ]);

        session()->flash('message', 'Pengajuan berhasil disetujui.');
        $this->closeModal();
    }

    public function tolak()
    {
        $this->validate(['catatan_keuangan' => 'required|string']); // Wajib isi alasan jika tolak

        $perjalanan = PerjalananDinas::findOrFail($this->perjalananId);
        $perjalanan->update([
            'status' => 'REJECTED',
            'finance_approved_by' => auth()->id(),
            'finance_approved_at' => now(),
            'catatan_keuangan' => $this->catatan_keuangan,
        ]);

        session()->flash('message', 'Pengajuan ditolak.');
        $this->closeModal();
    }
}