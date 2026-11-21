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
    public $editStep = 1; // 1 = Info Perjalanan, 2 = Rincian Biaya, 3 = Konfirmasi
    public $editId;

    // Data Perjalanan
    public $editProvinsi, $editKotaTujuanId, $editTanggalBerangkat, $editTanggalPulang, $editMaksud, $editTempatBerangkat, $editAlatAngkutan, $editTingkatBiaya;

    // Data Rincian
    public $editRincian = [];
    public $editTotalEstimasi = 0;

    public function openEditModal($id)
    {
        $perjalanan = PerjalananDinas::with(['kotaTujuan', 'rincianBiaya'])->findOrFail($id);

        // Validasi: Pemilik form ATAU Super Admin yang boleh edit
        $isSuperAdmin = Auth::user()->hasRole('super-admin');

        // FIX: cast ke int agar "5" (string dari DB) tidak dianggap berbeda dengan 5 (int)
        if ((int) $perjalanan->pegawai_id !== (int) Auth::id() && !$isSuperAdmin) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => 'Anda tidak berhak mengedit form ini.']);
            return;
        }
        if ($perjalanan->status !== 'DRAFT') {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => 'Form sudah diproses, tidak bisa diedit.']);
            return;
        }

        $this->editId = $perjalanan->id;
        $this->editProvinsi = $perjalanan->kotaTujuan->provinsi ?? null;
        $this->editKotaTujuanId = $perjalanan->kota_tujuan_id;

        // FIX: null-safe agar tidak error 500 kalau tanggal kosong / belum di-cast
        $this->editTanggalBerangkat = $perjalanan->tanggal_berangkat?->format('Y-m-d');
        $this->editTanggalPulang = $perjalanan->tanggal_pulang?->format('Y-m-d');

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
        if ($this->editStep === 1) {
            $this->validate([
                'editProvinsi' => 'required',
                'editKotaTujuanId' => 'required',
                'editTanggalBerangkat' => 'required|date',
                'editTanggalPulang' => 'required|date|after_or_equal:editTanggalBerangkat',
                'editMaksud' => 'required|string',
            ]);
            $this->editStep = 2;
        } elseif ($this->editStep === 2) {
            // Validasi rincian sebelum ke step 3 (opsional, bisa disesuaikan)
            $this->editStep = 3;
        }
    }

    public function prevEditStep()
    {
        if ($this->editStep > 1) {
            $this->editStep--;
        }
    }

    // Hitung ulang nominal saat qty / harga satuan diubah di modal.
    // FIX: di Livewire 3, $key berbentuk "0.qty" (2 bagian), bukan "editRincian.0.qty" (3 bagian).
    public function updatedEditRincian($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $field = $parts[1];

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

        $this->dispatch('swal:success', ['message' => 'Data UMK berhasil diperbarui!']);
    }

    public function render()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');

        // Super Admin melihat semua UMK, pegawai biasa hanya miliknya sendiri
        $query = PerjalananDinas::with(['kotaTujuan', 'laporan', 'pegawai']);

        if (!$isSuperAdmin) {
            $query->where('pegawai_id', $user->id);
        }

        $riwayats = $query->latest()->paginate(10);

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
            'isSuperAdmin' => $isSuperAdmin,
        ])->layout($isSuperAdmin ? 'layouts.super-admin' : 'layouts.app');
    }
}