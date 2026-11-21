<?php

namespace App\Livewire\MasterData;

use App\Models\Coa;

class CoaIndex extends BaseCrud
{
    protected function model(): string
    {
        return Coa::class;
    }

    protected function fields(): array
    {
        return ['kode', 'nama', 'aktif'];
    }

    protected function searchable(): array
    {
        return ['kode', 'nama'];
    }

    protected function orderBy(): string
    {
        return 'kode';
    }

    protected function entityLabel(): string
    {
        return 'COA';
    }

    protected function defaultValues(): array
    {
        return ['aktif' => true];
    }

    protected function rules(): array
    {
        return [
            'form.kode' => 'required|string|max:50|unique:coas,kode,' . ($this->modelId ?? 'NULL'),
            'form.nama' => 'required|string|max:255',
            'form.aktif' => 'boolean',
        ];
    }
}