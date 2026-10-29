<?php

namespace App\Livewire\MasterData;

use App\Models\TarifHari;
use App\Models\Kota;
use App\Models\Golongan;
use Livewire\Component;
use Livewire\WithPagination;

class TarifHariIndex extends Component
{
    use WithPagination;

    public $tarifId, $kota_id, $golongan_id, $nominal;
    public $isModalOpen = false;

    protected $rules = [
        'kota_id' => 'required|exists:kotas,id',
        'golongan_id' => 'required|exists:golongans,id',
        'nominal' => 'required|numeric|min:0',
    ];

    public function render()
    {
        // Eager loading relasi kota & golongan biar gak berat (N+1)
        $tarifs = TarifHari::with(['kota', 'golongan'])->orderBy('kota_id')->paginate(10);

        return view('livewire.master-data.tarif-hari-index', [
            'tarifs' => $tarifs,
            'kotas' => Kota::orderBy('nama')->get(),
            'golongans' => Golongan::orderBy('nama')->get()
        ])->layout('layouts.app');
    }

    public function openModal() { $this->resetFields(); $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    public function edit($id)
    {
        $tarif = TarifHari::findOrFail($id);
        $this->tarifId = $id;
        $this->kota_id = $tarif->kota_id;
        $this->golongan_id = $tarif->golongan_id;
        $this->nominal = $tarif->nominal;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();
        TarifHari::updateOrCreate(
            ['id' => $this->tarifId],
            [
                'kota_id' => $this->kota_id,
                'golongan_id' => $this->golongan_id,
                'nominal' => $this->nominal
            ]
        );
        session()->flash('message', $this->tarifId ? 'Tarif diupdate.' : 'Tarif ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        TarifHari::findOrFail($id)->delete();
        session()->flash('message', 'Tarif dihapus.');
    }

    private function resetFields() { $this->reset(['tarifId', 'kota_id', 'golongan_id', 'nominal']); $this->resetValidation(); }
}