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

    // Properti Filter & Search
    public $search = '';
    public $filterProvinsi = '';
    public $filterGolongan = '';

    protected $rules = [
        'kota_id' => 'required|exists:kotas,id',
        'golongan_id' => 'required|exists:golongans,id',
        'nominal' => 'required|numeric|min:0',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterProvinsi() { $this->resetPage(); $this->kota_id = ''; }
    public function updatingFilterGolongan() { $this->resetPage(); }

    public function render()
    {
        $query = TarifHari::with(['kota', 'golongan']);

        if ($this->search) {
            $query->whereHas('kota', fn ($q) => $q->where('nama', 'like', '%' . $this->search . '%'));
        }
        if ($this->filterProvinsi) {
            $query->whereHas('kota', fn ($q) => $q->where('provinsi', $this->filterProvinsi));
        }
        if ($this->filterGolongan) {
            $query->where('golongan_id', $this->filterGolongan);
        }

        $tarifs = $query->orderBy('kota_id')->paginate(10);
        $provinsis = Kota::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');
        
        $kotas = [];
        if ($this->filterProvinsi) {
            $kotas = Kota::where('provinsi', $this->filterProvinsi)->orderBy('nama')->get();
        } else {
            $kotas = Kota::orderBy('nama')->get();
        }

        return view('livewire.master-data.tarif-hari-index', [
            'tarifs' => $tarifs,
            'kotas' => $kotas,
            'provinsis' => $provinsis,
            'golongans' => Golongan::orderBy('nama')->get()
        ])->layout('layouts.app');
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
        $tarif = TarifHari::findOrFail($id);
        $this->tarifId = $id;
        $this->kota_id = $tarif->kota_id;
        $this->golongan_id = $tarif->golongan_id;
        $this->nominal = $tarif->nominal;
        
        if ($tarif->kota) {
            $this->filterProvinsi = $tarif->kota->provinsi;
        }
        
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        // Cek Duplikat
        $exists = TarifHari::where('kota_id', $this->kota_id)
            ->where('golongan_id', $this->golongan_id)
            ->when($this->tarifId, fn ($query) => $query->where('id', '!=', $this->tarifId))
            ->exists();

        if ($exists) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Data Duplikat!',
                'text' => 'Tarif untuk Kota dan Golongan ini sudah ada. Silakan edit data yang sudah ada.'
            ]);
            return;
        }

        TarifHari::updateOrCreate(
            ['id' => $this->tarifId],
            [
                'kota_id' => $this->kota_id,
                'golongan_id' => $this->golongan_id,
                'nominal' => $this->nominal
            ]
        );

        // SweetAlert Sukses
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => $this->tarifId ? 'Data tarif berhasil diperbarui.' : 'Data tarif berhasil ditambahkan.'
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        TarifHari::findOrFail($id)->delete();
        
        // SweetAlert Hapus Sukses
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Terhapus!',
            'text' => 'Data tarif berhasil dihapus.'
        ]);
    }

    private function resetFields() 
    { 
        $this->reset(['tarifId', 'kota_id', 'golongan_id', 'nominal']); 
        $this->resetValidation(); 
    }
}