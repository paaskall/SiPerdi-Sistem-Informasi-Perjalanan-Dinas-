<?php

namespace App\Actions\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\User;
use App\Services\NomorDokumenService;
use Illuminate\Support\Facades\DB;

class SimpanPerjalananAction
{
    public function __construct(
        private NomorDokumenService $nomorService,
    ) {}

    /**
     * Simpan atau update perjalanan dinas + rincian biaya.
     *
     * @param  User   $pegawai       Pemilik perjalanan
     * @param  array  $perjalananData Data perjalanan (dari FormObject)
     * @param  array  $rincian        Array rincian biaya
     * @param  int|null $editId       ID perjalanan kalau mode edit
     * @param  array  $nomorDokumen   Data nomor (no_pembukuan, akun, no_spd) — hanya untuk create
     */
    public function execute(
        User $pegawai,
        array $perjalananData,
        array $rincian,
        ?int $editId = null,
        array $nomorDokumen = [],
    ): PerjalananDinas {
        return DB::transaction(function () use (
            $pegawai, $perjalananData, $rincian, $editId, $nomorDokumen
        ) {
            $data = array_merge($perjalananData, [
                'pegawai_id' => $pegawai->id,
                'divisi_id' => $pegawai->divisi_id,
            ]);

            if ($editId) {
                $perjalanan = PerjalananDinas::findOrFail($editId);
                $perjalanan->update($data);
                $perjalanan->rincianBiaya()->delete();
            } else {
                $perjalanan = PerjalananDinas::create(array_merge($data, [
                    'nomor_pengajuan' => $this->nomorService->generateNomorPengajuan(),
                    'no_pembukuan' => $nomorDokumen['no_pembukuan'] ?? null,
                    'akun' => $nomorDokumen['akun'] ?? null,
                    'no_spd' => $nomorDokumen['no_spd'] ?? null,
                    'status' => 'DRAFT',
                    'nama_bendahara' => 'Suharna Sudirman, S.Kom',
                    'nip_bendahara' => '19810303 200902 2 006',
                    'nama_ppk' => 'Sudarmi Narwis, SE., MM',
                    'nip_ppk' => '19701030 200501 2 001',
                ]));
            }

            foreach ($rincian as $row) {
                $perjalanan->rincianBiaya()->create($row);
            }

            return $perjalanan;
        });
    }
}