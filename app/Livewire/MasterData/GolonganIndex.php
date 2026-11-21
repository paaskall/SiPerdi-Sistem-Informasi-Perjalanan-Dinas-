<?php

namespace App\Livewire\MasterData;

use App\Models\Golongan;

class GolonganIndex extends BaseCrud
{
    protected function model(): string
    {
        return Golongan::class;
    }

    protected function fields(): array
    {
        return ['nama'];
    }

    protected function orderBy(): string
    {
        return 'nama';
    }

    protected function entityLabel(): string
    {
        return 'Golongan';
    }

    protected function rules(): array
    {
        return [
            'form.nama' => 'required|string|max:50',
        ];
    }
}