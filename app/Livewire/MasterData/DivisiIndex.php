<?php

namespace App\Livewire\MasterData;

use App\Models\Divisi;

class DivisiIndex extends BaseCrud
{
    protected function model(): string
    {
        return Divisi::class;
    }

    protected function fields(): array
    {
        return ['nama', 'kode'];
    }

    protected function searchable(): array
    {
        return ['nama', 'kode'];
    }

    protected function orderBy(): string
    {
        return 'nama';
    }

    protected function entityLabel(): string
    {
        return 'Divisi';
    }

    protected function rules(): array
    {
        return [
            'form.nama' => 'required|string|max:255',
            'form.kode' => 'required|string|max:20|unique:divisis,kode,' . ($this->modelId ?? 'NULL'),
        ];
    }
}