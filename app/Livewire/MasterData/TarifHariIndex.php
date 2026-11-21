<?php

namespace App\Livewire\MasterData;

use App\Models\Golongan;
use App\Models\Kota;
use App\Models\TarifHari;

class TarifHariIndex extends BaseCrud
{
    public string $filterProvinsi = '';
    public string $filterGolongan = '';

    protected function model(): string
    {
        return TarifHari::class;
    }

    protected function fields(): array
    {
        return ['kota_id', 'golongan_id', 'nominal'];
    }

    protected function orderBy(): string
    {
        return 'kota_id';
    }

    protected function entityLabel(): string
    {
        return 'Tarif Harian';
    }

    protected function rules(): array
    {
        return [
            'form.kota_id' => 'required|exists:kotas,id',
            'form.golongan_id' => 'required|exists:golongans,id',
            'form.nominal' => 'required|numeric|min:0',
        ];
    }

    protected function beforeSave(array $data): array
    {
        $exists = TarifHari::where('kota_id', $data['kota_id'])
            ->where('golongan_id', $data['golongan_id'])
            ->when($this->modelId, fn ($q) => $q->where('id', '!=', $this->modelId))
            ->exists();

        if ($exists) {
            $this->swalError('Tarif untuk Kota dan Golongan ini sudah ada. Silakan edit data yang sudah ada.');
            return [];
        }

        return $data;
    }

    protected function extraViewData(): array
    {
        return [
            'provinsis' => Kota::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi'),
            'kotas' => $this->filterProvinsi
                ? Kota::where('provinsi', $this->filterProvinsi)->orderBy('nama')->get()
                : Kota::orderBy('nama')->get(),
            'golongans' => Golongan::orderBy('nama')->get(),
        ];
    }

    public function updatingFilterProvinsi(): void
    {
        $this->resetPage();
        $this->form['kota_id'] = null;
    }

    public function updatingFilterGolongan(): void
    {
        $this->resetPage();
    }

    // Override render: tambahkan filter extra di query
    public function render()
    {
        $query = TarifHari::with(['kota', 'golongan']);

        if ($this->search !== '') {
            $query->whereHas('kota', fn ($q) => $q->where('nama', 'like', "%{$this->search}%"));
        }

        if ($this->filterProvinsi) {
            $query->whereHas('kota', fn ($q) => $q->where('provinsi', $this->filterProvinsi));
        }

        if ($this->filterGolongan) {
            $query->where('golongan_id', $this->filterGolongan);
        }

        $tarifs = $query->orderBy($this->orderBy())->paginate($this->perPage());

        return view($this->viewName(), array_merge(
            ['tarifs' => $tarifs],
            $this->extraViewData()
        ))->layout($this->resolveLayout());
    }
}