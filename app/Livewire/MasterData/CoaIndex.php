<?php

namespace App\Livewire\MasterData;

use App\Models\Coa;
use Livewire\Component;
use Livewire\WithPagination;

class CoaIndex extends Component
{
    use WithPagination;

    public $coaId, $kode, $nama, $aktif = true;
    public $isModalOpen = false;
    public $search = '';

    protected $rules = [
        'kode' => 'required|string|max:50|unique:coas,kode',
        'nama' => 'required|string|max:255',
        'aktif' => 'boolean',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $coas = Coa::where('kode', 'like', '%'.$this->search.'%')
            ->orWhere('nama', 'like', '%'.$this->search.'%')
            ->orderBy('kode')->paginate(10);

        return view('livewire.master-data.coa-index', compact('coas'))->layout('layouts.app');
    }

    public function openModal() { $this->resetFields(); $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    public function edit($id)
    {
        $coa = Coa::findOrFail($id);
        $this->coaId = $id;
        $this->kode = $coa->kode;
        $this->nama = $coa->nama;
        $this->aktif = $coa->aktif;
        $this->rules['kode'] = 'required|string|max:50|unique:coas,kode,'.$id;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();
        Coa::updateOrCreate(
            ['id' => $this->coaId],
            ['kode' => $this->kode, 'nama' => $this->nama, 'aktif' => $this->aktif]
        );
        session()->flash('message', $this->coaId ? 'COA diupdate.' : 'COA ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Coa::findOrFail($id)->delete();
        session()->flash('message', 'COA dihapus.');
    }

    private function resetFields() { $this->reset(['coaId', 'kode', 'nama', 'aktif']); $this->resetValidation(); }
}