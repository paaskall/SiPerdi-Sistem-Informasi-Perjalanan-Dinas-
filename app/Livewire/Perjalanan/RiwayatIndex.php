<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\Kota;
use App\Models\Coa;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class RiwayatIndex extends Component
{
    use WithPagination;

    // Property untuk Modal Edit
    public $showEditModal = false;
    public $editStep = 1; // 1 = Info Perjalanan, 2 = Rincian Biaya
    public $editId;
    
    // Data Perjalanan
    public $editProvinsi, $editKotaTujuanId, $editTanggalBerangkat, $editTanggalPulang, $editMaksud, $editTempatBerangkat, $editAlatAngkutan, $editTingkatBiaya;
    
    // Data Rincian
    public $editRincian = [];
    public $editTotalEstimasi = 0;

    public function openEditModal($id)
    {
        $perjalanan = PerjalananDinas::with(['kotaTujuan', 'rincianBiaya'])->findOrFail($id);

        if ($perjalanan->pegawai_id !== Auth::id() || $perjalanan->status !== 'DRAFT') {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => 'Anda tidak berhak mengedit form ini atau form sudah diproses.']);
            return;
        }

        $this->editId = $perjalanan->id;
        $this->editProvinsi = $perjalanan->kotaTujuan->provinsi ?? null;
        $this->editKotaTujuanId = $perjalanan->kota_tujuan_id;
        $this->editTanggalBerangkat = $perjalanan->tanggal_berangkat->format('Y-m-d');
        $this->editTanggalPulang = $perjalanan->tanggal_pulang->format('Y-m-d');
        $this->editMaksud = $perjalanan->maksud_perjalanan;
        $this->editTempatBerangkat = $perjalanan->tempat_berangkat;
        $this->editAlatAngkutan = $perjalanan->alat_angkutan;
        $this->editTingkatBiaya = $perjalanan->tingkat_biaya;

        // Ambil data rincian biaya
        $this->editRincian = $perjalanan->rincianBiaya->map(function ($item) {
            return [
                'coa_id' => $item->coa_id,
                'keterangan' => $item->keterangan,
                'qty' => $item->qty,
                'satuan' => $item->satuan,
                'harga_satuan' => $item->harga_satuan,
                'nominal' => $item->nominal,
            ];
        })->toArray();

        $this->hitungEditTotal();
        $this->editStep = 1; // Reset ke langkah 1
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editStep = 1;
        $this->reset([
            'editId', 'editProvinsi', 'editKotaTujuanId', 'editTanggalBerangkat', 'editTanggalPulang', 
            'editMaksud', 'editTempatBerangkat', 'editAlatAngkutan', 'editTingkatBiaya', 
            'editRincian', 'editTotalEstimasi'
        ]);
    }

    public function nextEditStep()
    {
        $this->validate([
            'editProvinsi' => 'required',
            'editKotaTujuanId' => 'required',
            'editTanggalBerangkat' => 'required|date',
            'editTanggalPulang' => 'required|date|after_or_equal:editTanggalBerangkat',
            'editMaksud' => 'required|string',
        ]);

        $this->editStep = 2;
    }

    public function prevEditStep()
    {
        $this->editStep = 1;
    }

    // Fungsi untuk update qty/harga secara live di modal
    public function updatedEditRincian($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 3) {
            $index = (int) $parts[1];
            $field = $parts[2];

            if (in_array($field, ['qty', 'harga_satuan'])) {
                $qty = (float) ($this->editRincian[$index]['qty'] ?? 0);
                $harga = (float) ($this->editRincian[$index]['harga_satuan'] ?? 0);
                $this->editRincian[$index]['nominal'] = $qty * $harga;
            }
        }
        $this->hitungEditTotal();
    }

    public function hitungEditTotal()
    {
        $this->editTotalEstimasi = collect($this->editRincian)->sum('nominal');
    }

    public function updateUmk()
    {
        $this->validate([
            'editProvinsi' => 'required',
            'editKotaTujuanId' => 'required|exists:kotas,id',
            'editTanggalBerangkat' => 'required|date',
            'editTanggalPulang' => 'required|date|after_or_equal:editTanggalBerangkat',
            'editMaksud' => 'required|string',
            'editRincian.*.coa_id' => 'required|exists:coas,id',
            'editRincian.*.qty' => 'required|numeric|min:1',
            'editRincian.*.harga_satuan' => 'required|numeric|min:1',
        ]);

        $perjalanan = PerjalananDinas::findOrFail($this->editId);
        
        $start = \Carbon\Carbon::parse($this->editTanggalBerangkat);
        $end = \Carbon\Carbon::parse($this->editTanggalPulang);
        $jumlahHari = $start->diffInDays($end) + 1;

        $perjalanan->update([
            'kota_tujuan_id' => $this->editKotaTujuanId,
            'tanggal_berangkat' => $this->editTanggalBerangkat,
            'tanggal_pulang' => $this->editTanggalPulang,
            'jumlah_hari' => $jumlahHari,
            'maksud_perjalanan' => $this->editMaksud,
            'tempat_berangkat' => $this->editTempatBerangkat,
            'alat_angkutan' => $this->editAlatAngkutan,
            'tingkat_biaya' => $this->editTingkatBiaya,
            'total_estimasi' => $this->editTotalEstimasi,
        ]);

        // Hapus rincian lama, masukkan yang baru
        $perjalanan->rincianBiaya()->delete();
        foreach ($this->editRincian as $r) {
            $perjalanan->rincianBiaya()->create($r);
        }

        $this->closeEditModal();
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil!', 'text' => 'Data UMK berhasil diperbarui.']);
    }

    public function render()
    {
        $riwayats = PerjalananDinas::with(['kotaTujuan', 'laporan'])
            ->where('pegawai_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Data untuk form modal
        $provinsis = Kota::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');
        $kotas = [];
        if ($this->editProvinsi) {
            $kotas = Kota::where('provinsi', $this->editProvinsi)->orderBy('nama')->get();
        }
        $coas = Coa::where('aktif', true)->orderBy('kode')->get();

        return view('livewire.perjalanan.riwayat-index', [
            'riwayats' => $riwayats, 
            'provinsis' => $provinsis,
            'kotas' => $kotas,
            'coas' => $coas,
        ])->layout('layouts.app');
    }
}