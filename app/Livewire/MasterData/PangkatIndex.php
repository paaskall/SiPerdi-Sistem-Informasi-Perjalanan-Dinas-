<?php

namespace App\Livewire\MasterData;

use App\Models\Pangkat;
use Livewire\Component;
use Livewire\WithPagination;

class PangkatIndex extends Component
{
    use WithPagination;

    public $pangkatId, $nama, $golongan_ruang;
    public $isModalOpen = false;
    public $search = '';

    protected $rules = [
        'nama' => 'required|string|max:100',
        'golongan_ruang' => 'nullable|string|max:20',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $pangkats = Pangkat::where('nama', 'like', '%'.$this->search.'%')
            ->orWhere('golongan_ruang', 'like', '%'.$this->search.'%')
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.master-data.pangkat-index', compact('pangkats'))
            ->layout('layouts.app');
    }

    public function openModal()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function edit($id)
    {
        $pangkat = Pangkat::findOrFail($id);
        $this->pangkatId = $id;
        $this->nama = $pangkat->nama;
        $this->golongan_ruang = $pangkat->golongan_ruang;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        Pangkat::updateOrCreate(
            ['id' => $this->pangkatId],
            ['nama' => $this->nama, 'golongan_ruang' => $this->golongan_ruang]
        );

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => $this->pangkatId ? 'Pangkat berhasil diperbarui.' : 'Pangkat berhasil ditambahkan.',
            'icon' => 'success',
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        Pangkat::findOrFail($id)->delete();

        $this->dispatch('swal', [
            'title' => 'Terhapus!',
            'text' => 'Pangkat berhasil dihapus.',
            'icon' => 'success',
        ]);
    }

    private function resetFields()
    {
        $this->reset(['pangkatId', 'nama', 'golongan_ruang']);
        $this->resetValidation();
    }
}