<?php

namespace App\Livewire\Perjalanan;

use App\Models\Reimbursement;
use Livewire\Component;
use Livewire\WithPagination;

class ReimbursementIndex extends Component
{
    use WithPagination;

    public $reimbId, $catatan_keuangan;
    public $isModalOpen = false;

    public function render()
    {
        $query = Reimbursement::with(['perjalanan.pegawai', 'perjalanan.kotaTujuan']);
        
        // Tampilkan berdasarkan role
        if (auth()->user()->hasRole('keuangan')) {
            $query->where('status', 'SUBMITTED');
        } elseif (auth()->user()->hasRole('pimpinan')) {
            $query->where('status', 'FINANCE_APPROVED');
        }

        $reimbursements = $query->orderBy('created_at', 'asc')->paginate(10);

        return view('livewire.perjalanan.reimbursement-index', compact('reimbursements'))->layout('layouts.app');
    }

    public function openModal($id)
    {
        $this->resetValidation();
        $this->reimbId = $id;
        $this->catatan_keuangan = '';
        $this->isModalOpen = true;
    }

    public function closeModal() { $this->isModalOpen = false; }

    public function setujuiKeuangan()
    {
        $reimb = Reimbursement::findOrFail($this->reimbId);
        $reimb->update([
            'status' => 'FINANCE_APPROVED',
            'finance_approved_by' => auth()->id(),
            'finance_approved_at' => now(),
            'catatan_keuangan' => $this->catatan_keuangan,
        ]);
        session()->flash('message', 'Laporan Reimbursement disetujui Keuangan.');
        $this->closeModal();
    }

    public function tolakKeuangan()
    {
        $this->validate(['catatan_keuangan' => 'required']);
        $reimb = Reimbursement::findOrFail($this->reimbId);
        $reimb->update([
            'status' => 'REJECTED',
            'finance_approved_by' => auth()->id(),
            'finance_approved_at' => now(),
            'catatan_keuangan' => $this->catatan_keuangan,
        ]);
        session()->flash('message', 'Laporan Reimbursement ditolak.');
        $this->closeModal();
    }

    public function setujuiPimpinan()
    {
        $reimb = Reimbursement::findOrFail($this->reimbId);
        $reimb->update([
            'status' => 'PIMPINAN_SIGNED',
            'pimpinan_signed_by' => auth()->id(),
            'pimpinan_signed_at' => now(),
        ]);
        session()->flash('message', 'Laporan Reimbursement telah ditandatangani Pimpinan.');
        $this->closeModal();
    }
}