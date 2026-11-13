<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\LaporanPerjalanan;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormLaporan extends Component
{
    use WithFileUploads;

    public $perjalanan;
    public $ringkasan_kegiatan, $hasil_perjalanan, $kendala, $total_biaya_riil;
    public $file_lampiran;

    public function mount($id)
    {
        $this->perjalanan = PerjalananDinas::with('laporan')->findOrFail($id);
        
        if ($this->perjalanan->pegawai_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengakses ini.');
        }

        if (!in_array($this->perjalanan->status, ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED'])) {
            abort(403, 'Laporan hanya bisa diisi setelah UMK dicetak.');
        }

        if ($this->perjalanan->laporan) {
            session()->flash('error', 'Laporan untuk perjalanan ini sudah pernah dibuat.');
            
            $from = request()->query('from', 'riwayat');
            if ($from === 'laporan') {
                return redirect()->route('laporan.riwayat');
            }
            return redirect()->route('perjalanan.riwayat');
        }

        $this->total_biaya_riil = $this->perjalanan->total_estimasi;
    }

    public function simpan()
    {
        $this->validate([
            'ringkasan_kegiatan' => 'required|string|min:10',
            'hasil_perjalanan' => 'required|string|min:10',
            'kendala' => 'nullable|string',
            'total_biaya_riil' => 'required|numeric|min:1',
            // UBAH DI SINI: Gunakan 'extensions' bukan 'mimes'
            'file_lampiran' => 'nullable|file|extensions:pdf,jpg,jpeg,png|max:10240',
        ], [
            'ringkasan_kegiatan.required' => 'Ringkasan kegiatan wajib diisi.',
            'ringkasan_kegiatan.min' => 'Ringkasan kegiatan minimal 10 karakter.',
            'hasil_perjalanan.required' => 'Hasil perjalanan wajib diisi.',
            'hasil_perjalanan.min' => 'Hasil perjalanan minimal 10 karakter.',
            'total_biaya_riil.required' => 'Total biaya riil wajib diisi.',
            'total_biaya_riil.min' => 'Total biaya riil minimal Rp 1.',
            'file_lampiran.extensions' => 'Lampiran harus berupa PDF atau Gambar (JPG/JPEG/PNG).',
            'file_lampiran.max' => 'Ukuran lampiran maksimal 10MB.',
            'file_lampiran.file' => 'Lampiran gagal diunggah. Coba file lain.',
        ]);

        $path = null;
        if ($this->file_lampiran) {
            $path = $this->file_lampiran->store('lampiran_laporan', 'public');
        }

        LaporanPerjalanan::create([
            'perjalanan_id' => $this->perjalanan->id,
            'pegawai_id' => auth()->id(),
            'ringkasan_kegiatan' => $this->ringkasan_kegiatan,
            'hasil_perjalanan' => $this->hasil_perjalanan,
            'kendala' => $this->kendala,
            'total_biaya_riil' => $this->total_biaya_riil,
            'file_lampiran' => $path,
            'status' => 'SUBMITTED',
        ]);

        session()->flash('message', 'Laporan berhasil dikirim!');
        
        $from = request()->query('from', 'riwayat');
        if ($from === 'laporan') {
            return redirect()->route('laporan.riwayat');
        }
        return redirect()->route('perjalanan.riwayat');
    }

    public function render()
    {
        return view('livewire.perjalanan.form-laporan')->layout('layouts.app');
    }
}