<?php

namespace App\Livewire\MasterData;

use App\Models\Kota;
use Livewire\Component;
use Livewire\WithPagination;

class KotaIndex extends Component
{
    use WithPagination;

    public $kotaId, $nama, $provinsi;
    public $isModalOpen = false;
    public $search = '';

    protected $rules = [
        'nama' => 'required|string|max:255',
        'provinsi' => 'nullable|string|max:255',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $kotas = Kota::where('nama', 'like', '%'.$this->search.'%')
            ->orWhere('provinsi', 'like', '%'.$this->search.'%')
            ->orderBy('nama')->paginate(10);

        return view('livewire.master-data.kota-index', compact('kotas'))->layout('layouts.app');
    }

    public function openModal() { $this->resetFields(); $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    public function edit($id)
    {
        $kota = Kota::findOrFail($id);
        $this->kotaId = $id;
        $this->nama = $kota->nama;
        $this->provinsi = $kota->provinsi;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();
        Kota::updateOrCreate(
            ['id' => $this->kotaId],
            ['nama' => $this->nama, 'provinsi' => $this->provinsi]
        );
        session()->flash('message', $this->kotaId ? 'Kota diupdate.' : 'Kota ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Kota::findOrFail($id)->delete();
        session()->flash('message', 'Kota dihapus.');
    }

    private function resetFields() { $this->reset(['kotaId', 'nama', 'provinsi']); $this->resetValidation(); }
}