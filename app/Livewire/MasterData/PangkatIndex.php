<?php

namespace App\Livewire\MasterData;

use App\Models\Pangkat;

class PangkatIndex extends BaseCrud
{
    protected function model(): string
    {
        return Pangkat::class;
    }

    protected function fields(): array
    {
        return ['nama', 'golongan_ruang'];
    }

    protected function entityLabel(): string
    {
        return 'Pangkat';
    }

    protected function rules(): array
    {
        return [
            'form.nama' => 'required|string|max:100',
            'form.golongan_ruang' => 'nullable|string|max:20',
        ];
    }
}