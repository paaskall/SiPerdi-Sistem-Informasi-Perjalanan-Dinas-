<?php

namespace App\Livewire\MasterData;

use App\Models\Kota;

class KotaIndex extends BaseCrud
{
    protected function model(): string
    {
        return Kota::class;
    }

    protected function fields(): array
    {
        return ['nama', 'provinsi'];
    }

    protected function orderBy(): string
    {
        return 'nama';
    }

    protected function entityLabel(): string
    {
        return 'Kota';
    }

    protected function rules(): array
    {
        return [
            'form.nama' => 'required|string|max:255',
            'form.provinsi' => 'nullable|string|max:255',
        ];
    }
}