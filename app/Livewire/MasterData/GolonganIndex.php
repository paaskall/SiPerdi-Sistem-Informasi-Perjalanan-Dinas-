<?php

namespace App\Livewire\MasterData;

use App\Models\Golongan;
use Livewire\Component;
use Livewire\WithPagination;

class GolonganIndex extends Component
{
    use WithPagination;

    public $golonganId, $nama;
    public $isModalOpen = false;
    public $search = '';

    protected $rules = [
        'nama' => 'required|string|max:50',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $golongans = Golongan::where('nama', 'like', '%'.$this->search.'%')
            ->orderBy('nama')->paginate(10);

        return view('livewire.master-data.golongan-index', compact('golongans'))
            ->layout('layouts.app');
    }

    public function openModal() { $this->resetFields(); $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    public function edit($id)
    {
        $gol = Golongan::findOrFail($id);
        $this->golonganId = $id;
        $this->nama = $gol->nama;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();
        Golongan::updateOrCreate(['id' => $this->golonganId], ['nama' => $this->nama]);
        session()->flash('message', $this->golonganId ? 'Golongan diupdate.' : 'Golongan ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Golongan::findOrFail($id)->delete();
        session()->flash('message', 'Golongan dihapus.');
    }

    private function resetFields() { $this->reset(['golonganId', 'nama']); $this->resetValidation(); }
}