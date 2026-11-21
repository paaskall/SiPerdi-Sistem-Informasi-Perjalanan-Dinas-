<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\PerjalananDinas;

class NomorDokumenService
{
    /**
     * Generate nomor dokumen untuk UMK baru.
     *
     * @return array{no_pembukuan: string, akun: string, no_spd: string}
     */
    public function generate(): array
    {
        $count = PerjalananDinas::whereYear('created_at', date('Y'))->count() + 1;

        return [
            'no_pembukuan' => 'PB/' . date('Y') . '/' . str_pad($count, 3, '0', STR_PAD_LEFT),
            'akun' => $this->defaultAkun(),
            'no_spd' => (2866 + $count) . '/PLB.1.2.1/SDM.04.1',
        ];
    }

    /**
     * Generate nomor pengajuan (dipanggil saat simpan).
     */
    public function generateNomorPengajuan(): string
    {
        $count = PerjalananDinas::whereYear('created_at', date('Y'))->count() + 1;

        return str_pad($count, 3, '0', STR_PAD_LEFT) . '/UMK/' . date('Y');
    }

    private function defaultAkun(): string
    {
        return Akun::where('aktif', true)->orderBy('kode')->value('kode')
            ?? '086.01.CO.4825.ABQ.001.051.A';
    }
}