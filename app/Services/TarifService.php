<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\TarifHari;
use App\Models\User;

class TarifService
{
    /**
     * Proses auto-sync saat COA dipilih di rincian.
     * - Penginapan → qty = jumlah_hari - 1, satuan = malam
     * - Uang Harian → qty = jumlah_hari, satuan = OH, auto-fill tarif
     */
    public function syncRincianItem(
        array $item,
        int $jumlahHari,
        ?int $kotaId,
        ?int $golonganId,
    ): array {
        if (empty($item['coa_id'])) {
            return $item;
        }

        $coa = Coa::find($item['coa_id']);
        if (! $coa) {
            return $item;
        }

        $nama = strtolower($coa->nama);

        if ($this->isPenginapan($nama)) {
            $item['qty'] = max(1, $jumlahHari - 1);
            $item['satuan'] = 'malam';
        } elseif ($this->isUangHarian($nama)) {
            $item['qty'] = $jumlahHari;
            $item['satuan'] = 'OH';

            if ($kotaId && $golonganId) {
                $tarif = $this->cariTarif($kotaId, $golonganId);
                if ($tarif) {
                    $item['harga_satuan'] = (float) $tarif->nominal;
                    $item['nominal'] = (float) $item['qty'] * (float) $tarif->nominal;
                }
            }
        }

        if (empty($item['harga_satuan'])) {
            $item['harga_satuan'] = 0;
        }

        return $item;
    }

    /**
     * Isi semua baris "uang harian" dengan tarif dari master.
     * Dipakai saat kota tujuan berubah.
     */
    public function fillTarifHarianForAll(
        array $rincian,
        int $kotaId,
        ?int $golonganId,
        ?int $onlyIndex = null,
    ): array {
        if (! $golonganId) {
            return $rincian;
        }

        $tarif = $this->cariTarif($kotaId, $golonganId);
        if (! $tarif) {
            return $rincian;
        }

        foreach ($rincian as $i => $item) {
            if ($onlyIndex !== null && $i !== $onlyIndex) {
                continue;
            }

            $coa = ! empty($item['coa_id']) ? Coa::find($item['coa_id']) : null;
            if (! $coa) {
                continue;
            }

            if ($this->isUangHarian(strtolower($coa->nama))) {
                $qty = (float) ($item['qty'] ?? 1);
                $rincian[$i]['harga_satuan'] = (float) $tarif->nominal;
                $rincian[$i]['nominal'] = $qty * (float) $tarif->nominal;
            }
        }

        return $rincian;
    }

    /**
     * Ambil ID Golongan yang relevan:
     * - Super admin + pilih pegawai → golongan pegawai
     * - Selain itu → golongan user login
     */
    public function resolveGolonganId(
        bool $isSuperAdmin,
        ?int $pegawaiId,
        ?User $currentUser = null,
    ): ?int {
        if ($isSuperAdmin && $pegawaiId) {
            return User::find($pegawaiId)?->golongan_id;
        }

        return ($currentUser ?? auth()->user())?->golongan_id;
    }

    /**
     * Hitung total dari array rincian.
     */
    public function hitungTotal(array $rincian): float
    {
        return collect($rincian)->sum(fn ($r) => (float) ($r['nominal'] ?? 0));
    }

    /**
     * Recalculate nominal per baris dari qty × harga_satuan.
     */
    public function recalculateNominal(array $rincian): array
    {
        foreach ($rincian as $i => $r) {
            $rincian[$i]['nominal'] = (float) ($r['qty'] ?? 1) * (float) ($r['harga_satuan'] ?? 0);
        }
        return $rincian;
    }

    // =========================================================
    // PRIVATE
    // =========================================================

    private function isPenginapan(string $nama): bool
    {
        return str_contains($nama, 'penginapan')
            || str_contains($nama, 'hotel')
            || str_contains($nama, 'akomodasi');
    }

    private function isUangHarian(string $nama): bool
    {
        return str_contains($nama, 'uang harian')
            || str_contains($nama, 'uang saku')
            || str_contains($nama, 'harian');
    }

    private function cariTarif(int $kotaId, int $golonganId): ?TarifHari
    {
        return TarifHari::where('kota_id', $kotaId)
            ->where('golongan_id', $golonganId)
            ->first();
    }
}