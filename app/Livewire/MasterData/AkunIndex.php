<?php

namespace App\Livewire\MasterData;

use App\Models\Akun;
use App\Models\PerjalananDinas;
use Illuminate\Database\Eloquent\Model;

class AkunIndex extends BaseCrud
{
    protected function model(): string
    {
        return Akun::class;
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
        return 'Akun';
    }

    protected function defaultValues(): array
    {
        return ['aktif' => true];
    }

    protected function rules(): array
    {
        return [
            'form.kode' => 'required|string|max:50|unique:akuns,kode,' . ($this->modelId ?? 'NULL'),
            'form.nama' => 'required|string|max:255',
            'form.aktif' => 'boolean',
        ];
    }

    protected function beforeDelete(Model $model): ?string
    {
        if (PerjalananDinas::where('akun', $model->kode)->exists()) {
            return 'Akun tidak bisa dihapus karena sudah dipakai di perjalanan dinas. Nonaktifkan saja.';
        }

        return null;
    }
}