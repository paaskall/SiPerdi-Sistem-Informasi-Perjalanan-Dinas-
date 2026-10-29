<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\Kota;
use App\Models\Coa;
use Livewire\Component;

class FormPengajuan extends Component
{
    public $kota_tujuan_id, $tanggal_berangkat, $tanggal_pulang, $jumlah_hari = 0, $maksud_perjalanan;
    public $rincian = []; 
    public $total_estimasi = 0;

    public function mount()
    {
        // Inisialisasi 1 baris rincian kosong
        $this->tambahBaris();
    }

    public function updated($property)
    {
        // Hitung ulang hari jika tanggal berubah
        if (in_array($property, ['tanggal_berangkat', 'tanggal_pulang'])) {
            if ($this->tanggal_berangkat && $this->tanggal_pulang) {
                $start = \Carbon\Carbon::parse($this->tanggal_berangkat);
                $end = \Carbon\Carbon::parse($this->tanggal_pulang);
                $this->jumlah_hari = $start->diffInDays($end) + 1; // Termasuk hari pertama
            }
        }

        // Hitung ulang total jika nominal berubah
        if (str_contains($property, 'rincian')) {
            $this->hitungTotal();
        }
    }

    public function tambahBaris()
    {
        $this->rincian[] = ['coa_id' => '', 'keterangan' => '', 'nominal' => 0];
    }

    public function hapusBaris($index)
    {
        unset($this->rincian[$index]);
        $this->rincian = array_values($this->rincian); // Reset index array
        $this->hitungTotal();
    }

    private function hitungTotal()
    {
        $this->total_estimasi = collect($this->rincian)->sum('nominal');
    }

    public function simpan()
    {
        $this->validate([
            'kota_tujuan_id' => 'required|exists:kotas,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'required|date|after_or_equal:tanggal_berangkat',
            'maksud_perjalanan' => 'required|string',
            'rincian.*.coa_id' => 'required|exists:coas,id',
            'rincian.*.nominal' => 'required|numeric|min:1',
        ]);

        // Generate Nomor Pengajuan: 001/PD/2026 (Sederhana dulu)
        $count = PerjalananDinas::whereYear('created_at', date('Y'))->count() + 1;
        $nomor = str_pad($count, 3, '0', STR_PAD_LEFT) . '/PD/' . date('Y');

        $perjalanan = PerjalananDinas::create([
            'nomor_pengajuan' => $nomor,
            'pegawai_id' => auth()->id(),
            'divisi_id' => auth()->user()->divisi_id,
            'kota_tujuan_id' => $this->kota_tujuan_id,
            'tanggal_berangkat' => $this->tanggal_berangkat,
            'tanggal_pulang' => $this->tanggal_pulang,
            'jumlah_hari' => $this->jumlah_hari,
            'maksud_perjalanan' => $this->maksud_perjalanan,
            'total_estimasi' => $this->total_estimasi,
            'status' => 'SUBMITTED',
        ]);

        foreach ($this->rincian as $r) {
            $perjalanan->rincianBiaya()->create([
                'coa_id' => $r['coa_id'],
                'keterangan' => $r['keterangan'],
                'nominal' => $r['nominal'],
            ]);
        }

        session()->flash('message', 'Pengajuan berhasil dikirim! Menunggu verifikasi Keuangan.');
        return redirect()->route('perjalanan.riwayat');
    }

    public function render()
    {
        return view('livewire.perjalanan.form-pengajuan', [
            'kotas' => Kota::orderBy('nama')->get(),
            'coas' => Coa::where('aktif', true)->orderBy('kode')->get(),
        ])->layout('layouts.app');
    }
}