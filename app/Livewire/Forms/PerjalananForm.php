<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class PerjalananForm extends Form
{
    // Data perjalanan
    public ?string $provinsi = null;
    public ?int $kota_tujuan_id = null;
    public ?string $tanggal_berangkat = null;
    public ?string $tanggal_pulang = null;
    public int $jumlah_hari = 0;
    public ?string $maksud_perjalanan = null;
    public ?string $tempat_berangkat = null;
    public string $alat_angkutan = 'Pesawat';
    public string $tingkat_biaya = 'C';
    public ?string $akun = null;
    public ?string $no_spd = null;
    public ?string $no_pembukuan = null;

    // Rincian biaya
    public array $rincian = [];
    public float $total_estimasi = 0;

    // Super admin / pegawai manual
    public bool $isSuperAdmin = false;
    public ?int $pegawai_id_pilihan = null;
    public bool $is_pegawai_manual = false;
    public ?string $nama_pegawai_manual = null;
    public ?string $nip_pegawai_manual = null;
    public ?int $golongan_id_manual = null;
    public ?int $divisi_id_manual = null;

    // =========================================================
    // RULES
    // =========================================================

    public function rules(): array
    {
        return [
            'provinsi' => 'required|string',
            'kota_tujuan_id' => 'required|exists:kotas,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'required|date|after_or_equal:tanggal_berangkat',
            'maksud_perjalanan' => 'required|string',
            'tempat_berangkat' => 'required|string',
            'alat_angkutan' => 'required|string',
            'tingkat_biaya' => 'required|in:A,B,C,D',
            'akun' => 'required|string|exists:akuns,kode',
            'no_spd' => 'required|string|max:100',
            'rincian.*.coa_id' => 'required|exists:coas,id',
            'rincian.*.qty' => 'required|numeric|min:1',
            'rincian.*.harga_satuan' => 'required|numeric|min:1',
        ];
    }

    public function manualRules(): array
    {
        return [
            'nama_pegawai_manual' => 'required|string|max:255',
            'golongan_id_manual' => 'required|exists:golongans,id',
            'divisi_id_manual' => 'nullable|exists:divisis,id',
            'nip_pegawai_manual' => 'nullable|string|max:30|unique:users,nip',
        ];
    }

    public function manualMessages(): array
    {
        return [
            'nama_pegawai_manual.required' => 'Nama pegawai wajib diisi.',
            'golongan_id_manual.required' => 'Golongan wajib dipilih.',
            'nip_pegawai_manual.unique' => 'NIP ini sudah terdaftar di sistem.',
        ];
    }

    // =========================================================
    // LOAD FROM MODEL (edit mode)
    // =========================================================

    public function setPerjalanan(\App\Models\PerjalananDinas $perjalanan): void
    {
        $this->provinsi = $perjalanan->kotaTujuan->provinsi ?? null;
        $this->kota_tujuan_id = $perjalanan->kota_tujuan_id;
        $this->tanggal_berangkat = $perjalanan->tanggal_berangkat?->format('Y-m-d');
        $this->tanggal_pulang = $perjalanan->tanggal_pulang?->format('Y-m-d');
        $this->jumlah_hari = $perjalanan->jumlah_hari;
        $this->maksud_perjalanan = $perjalanan->maksud_perjalanan;
        $this->tempat_berangkat = $perjalanan->tempat_berangkat;
        $this->alat_angkutan = $perjalanan->alat_angkutan;
        $this->tingkat_biaya = $perjalanan->tingkat_biaya;
        $this->akun = $perjalanan->akun;
        $this->no_spd = $perjalanan->no_spd;
        $this->no_pembukuan = $perjalanan->no_pembukuan;

        $this->rincian = $perjalanan->rincianBiaya->map(fn ($item) => [
            'coa_id' => $item->coa_id,
            'keterangan' => $item->keterangan,
            'qty' => $item->qty,
            'satuan' => $item->satuan,
            'harga_satuan' => $item->harga_satuan,
            'nominal' => $item->nominal,
        ])->toArray();

        $this->total_estimasi = (float) $perjalanan->total_estimasi;
    }

    // =========================================================
    // DATA UNTUK PERSISTENCE
    // =========================================================

    public function toPerjalananData(): array
    {
        return [
            'kota_tujuan_id' => $this->kota_tujuan_id,
            'tempat_berangkat' => $this->tempat_berangkat,
            'alat_angkutan' => $this->alat_angkutan,
            'tingkat_biaya' => $this->tingkat_biaya,
            'tanggal_berangkat' => $this->tanggal_berangkat,
            'tanggal_pulang' => $this->tanggal_pulang,
            'jumlah_hari' => $this->jumlah_hari,
            'maksud_perjalanan' => $this->maksud_perjalanan,
            'total_estimasi' => $this->total_estimasi,
        ];
    }

    public function toNomorDokumenData(): array
    {
        return [
            'no_pembukuan' => $this->no_pembukuan,
            'akun' => $this->akun,
            'no_spd' => $this->no_spd,
        ];
    }

    // =========================================================
    // RESET
    // =========================================================

    public function resetForm(): void
    {
        $this->reset();
        $this->resetValidation();
    }
}