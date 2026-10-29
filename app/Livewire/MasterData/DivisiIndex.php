<?php

namespace App\Livewire\MasterData;

use App\Models\Divisi;
use Livewire\Component;
use Livewire\WithPagination;

class DivisiIndex extends Component
{
    use WithPagination;

    public $divisiId, $nama, $kode;
    public $isModalOpen = false;
    public $search = '';

    protected $rules = [
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|max:20|unique:divisis,kode',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $divisis = Divisi::where('nama', 'like', '%'.$this->search.'%')
            ->orWhere('kode', 'like', '%'.$this->search.'%')
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.master-data.divisi-index', compact('divisis'))
            ->layout('layouts.app'); // pakai layout Breeze
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
        $divisi = Divisi::findOrFail($id);
        $this->divisiId = $id;
        $this->nama = $divisi->nama;
        $this->kode = $divisi->kode;
        
        // Ubah aturan unique agar mengabaikan ID saat ini
        $this->rules['kode'] = 'required|string|max:20|unique:divisis,kode,'.$id;
        
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();
        Divisi::updateOrCreate(
            ['id' => $this->divisiId],
            ['nama' => $this->nama, 'kode' => $this->kode]
        );

        // Ganti session flash dengan dispatch swal
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => $this->divisiId ? 'Data divisi berhasil diperbarui.' : 'Data divisi berhasil ditambahkan.',
            'icon' => 'success',
        ]);
        
        $this->closeModal();
    }

    public function delete($id)
    {
        Divisi::findOrFail($id)->delete();
        
        $this->dispatch('swal', [
            'title' => 'Terhapus!',
            'text' => 'Data divisi telah berhasil dihapus.',
            'icon' => 'success',
        ]);
    }

    private function resetFields()
    {
        $this->reset(['divisiId', 'nama', 'kode']);
        $this->resetValidation();
    }
}