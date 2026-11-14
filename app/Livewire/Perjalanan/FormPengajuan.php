<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\Kota;
use App\Models\Coa;
use App\Models\TarifHari;
use Livewire\Component;

class FormPengajuan extends Component
{
    public $provinsi; 
    public $kota_tujuan_id, $tanggal_berangkat, $tanggal_pulang, $jumlah_hari = 0, $maksud_perjalanan;
    public $tempat_berangkat, $alat_angkutan = 'Pesawat', $tingkat_biaya = 'C';
    public $no_pembukuan, $akun, $no_spd;
    public $rincian = [];
    public $total_estimasi = 0;

    public $profilLengkap = false;
    public $missing = [];

    public function mount()
    {
        $user = auth()->user();
        $this->profilLengkap = $user->profilLengkap();
        $this->missing = $user->dataProfilKurang();

        $editId = request()->query('edit');

        if ($editId) {
            $perjalanan = PerjalananDinas::with(['rincianBiaya', 'kotaTujuan'])->findOrFail($editId);

            if ($perjalanan->pegawai_id !== $user->id || $perjalanan->status !== 'DRAFT') {
                abort(403, 'Anda tidak memiliki akses untuk mengedit form ini atau form sudah diproses.');
            }

            $this->no_pembukuan = $perjalanan->no_pembukuan;
            $this->akun = $perjalanan->akun;
            $this->no_spd = $perjalanan->no_spd;
            $this->tempat_berangkat = $perjalanan->tempat_berangkat;
            $this->provinsi = $perjalanan->kotaTujuan->provinsi ?? null;
            $this->kota_tujuan_id = $perjalanan->kota_tujuan_id;
            $this->maksud_perjalanan = $perjalanan->maksud_perjalanan;
            $this->alat_angkutan = $perjalanan->alat_angkutan;
            $this->tingkat_biaya = $perjalanan->tingkat_biaya;
            
            $this->tanggal_berangkat = $perjalanan->tanggal_berangkat ? $perjalanan->tanggal_berangkat->format('Y-m-d') : null;
            $this->tanggal_pulang = $perjalanan->tanggal_pulang ? $perjalanan->tanggal_pulang->format('Y-m-d') : null;
            
            if ($this->tanggal_berangkat && $this->tanggal_pulang) {
                $start = \Carbon\Carbon::parse($this->tanggal_berangkat);
                $end = \Carbon\Carbon::parse($this->tanggal_pulang);
                $this->jumlah_hari = $start->diffInDays($end) + 1;
            }

            $this->rincian = $perjalanan->rincianBiaya->map(function ($item) {
                return [
                    'coa_id' => $item->coa_id,
                    'keterangan' => $item->keterangan,
                    'qty' => $item->qty,
                    'satuan' => $item->satuan,
                    'harga_satuan' => $item->harga_satuan,
                    'nominal' => $item->nominal,
                ];
            })->toArray();

            $this->hitungTotal();

        } else {
            $this->tempat_berangkat = 'Makassar';

            $count = PerjalananDinas::whereYear('created_at', date('Y'))->count() + 1;
            $this->no_pembukuan = 'PB/' . date('Y') . '/' . str_pad($count, 3, '0', STR_PAD_LEFT);
            $this->akun = '086.01.CO.4825.ABQ.001.051.A.524111';
            $this->no_spd = (2866 + $count) . '/PLB.1.2.1/SDM.04.1';

            $this->tambahBaris();
        }
    }

    public function updatedProvinsi()
    {
        $this->kota_tujuan_id = null;
    }

    // ✅ FUNGSI BARU: AUTO-FILL TARIF HARIAN SAAT KOTA DIPILIH
    public function updatedKotaTujuanId()
    {
        if (!$this->kota_tujuan_id) return;

        $user = auth()->user();
        
        // Cari tarif berdasarkan kota dan golongan user
        $tarif = TarifHari::where('kota_id', $this->kota_tujuan_id)
            ->where('golongan_id', $user->golongan_id)
            ->first();

        if ($tarif) {
            // Cari baris rincian yang COA-nya mengandung "uang harian" atau "harian"
            foreach ($this->rincian as $index => $item) {
                $coa = $item['coa_id'] ? Coa::find($item['coa_id']) : null;
                $namaCoa = $coa ? strtolower($coa->nama) : '';
                
                if (str_contains($namaCoa, 'uang harian') || str_contains($namaCoa, 'harian')) {
                    $qty = (float) ($this->rincian[$index]['qty'] ?? 1);
                    $this->rincian[$index]['harga_satuan'] = (float) $tarif->nominal;
                    $this->rincian[$index]['nominal'] = $qty * $tarif->nominal;
                }
            }
            $this->hitungTotal();
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['tanggal_berangkat', 'tanggal_pulang'])) {
            if ($this->tanggal_berangkat && $this->tanggal_pulang) {
                $start = \Carbon\Carbon::parse($this->tanggal_berangkat);
                $end = \Carbon\Carbon::parse($this->tanggal_pulang);
                $this->jumlah_hari = $start->diffInDays($end) + 1;
            } else {
                $this->jumlah_hari = 0;
            }
        }

        if (str_contains($property, 'rincian.') && str_ends_with($property, '.coa_id')) {
            $parts = explode('.', $property);
            $index = (int) $parts[1];

            if (!empty($this->rincian[$index]['coa_id'])) {
                $coa = Coa::find($this->rincian[$index]['coa_id']);
                $namaCoa = $coa ? strtolower($coa->nama) : '';

                if (str_contains($namaCoa, 'penginapan') || str_contains($namaCoa, 'hotel') || str_contains($namaCoa, 'akomodasi')) {
                    $qtyPenginapan = max(1, $this->jumlah_hari - 1);
                    $this->rincian[$index]['qty'] = $qtyPenginapan;
                    $this->rincian[$index]['satuan'] = 'malam';
                } 
                elseif (str_contains($namaCoa, 'uang harian') || str_contains($namaCoa, 'uang saku') || str_contains($namaCoa, 'harian')) {
                    $qtyHarian = $this->jumlah_hari; // Full sejumlah hari
                    $this->rincian[$index]['qty'] = $qtyHarian;
                    $this->rincian[$index]['satuan'] = 'OH';

                    // ✅ AUTO-FILL HARGA SAAT COA HARIAN DIPILIH (JIKA KOTA SUDAH ADA)
                    if ($this->kota_tujuan_id) {
                        $tarif = TarifHari::where('kota_id', $this->kota_tujuan_id)
                            ->where('golongan_id', auth()->user()->golongan_id)
                            ->first();
                        
                        if ($tarif) {
                            $this->rincian[$index]['harga_satuan'] = (float) $tarif->nominal;
                        }
                    }
                }

                if(empty($this->rincian[$index]['harga_satuan'])){
                    $this->rincian[$index]['harga_satuan'] = 0;
                }
            }
        }

        if (str_contains($property, 'rincian')) {
            $this->hitungTotal();
        }
    }

    public function tambahBaris()
    {
        $this->rincian[] = [
            'coa_id' => '', 'keterangan' => '',
            'qty' => 1, 'satuan' => 'kali', 'harga_satuan' => 0, 'nominal' => 0
        ];
    }

    public function hapusBaris($index)
    {
        unset($this->rincian[$index]);
        $this->rincian = array_values($this->rincian);
        $this->hitungTotal();
    }

    public function hitungTotal()
    {
        foreach ($this->rincian as $i => $r) {
            $this->rincian[$i]['nominal'] = (float) ($r['qty'] ?? 1) * (float) ($r['harga_satuan'] ?? 0);
        }
        $this->total_estimasi = collect($this->rincian)->sum('nominal');
    }

    public function simpan()
    {
        $user = auth()->user();
        if (! $user->profilLengkap()) {
            $this->addError('profil', 'Data profil belum lengkap.');
            return;
        }

        $this->validate([
            'provinsi' => 'required|string',
            'kota_tujuan_id' => 'required|exists:kotas,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_pulang' => 'required|date|after_or_equal:tanggal_berangkat',
            'maksud_perjalanan' => 'required|string',
            'tempat_berangkat' => 'required|string',
            'alat_angkutan' => 'required|string',
            'tingkat_biaya' => 'required|in:A,B,C,D',
            'rincian.*.coa_id' => 'required|exists:coas,id',
            'rincian.*.qty' => 'required|numeric|min:1',
            'rincian.*.harga_satuan' => 'required|numeric|min:1',
        ]);

        $perjalanan = PerjalananDinas::create([
            'nomor_pengajuan' => str_pad(PerjalananDinas::whereYear('created_at', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT) . '/UMK/' . date('Y'),
            'no_pembukuan' => $this->no_pembukuan,
            'akun' => $this->akun,
            'no_spd' => $this->no_spd,
            'pegawai_id' => $user->id,
            'divisi_id' => $user->divisi_id,
            'kota_tujuan_id' => $this->kota_tujuan_id,
            'tempat_berangkat' => $this->tempat_berangkat,
            'alat_angkutan' => $this->alat_angkutan,
            'tingkat_biaya' => $this->tingkat_biaya,
            'tanggal_berangkat' => $this->tanggal_berangkat,
            'tanggal_pulang' => $this->tanggal_pulang,
            'jumlah_hari' => $this->jumlah_hari,
            'maksud_perjalanan' => $this->maksud_perjalanan,
            'total_estimasi' => $this->total_estimasi,
            'status' => 'DRAFT',
            'nama_bendahara' => 'Suharna Sudirman, S.Kom',
            'nip_bendahara' => '19810303 200902 2 006',
            'nama_ppk' => 'Sudarmi Narwis, SE., MM',
            'nip_ppk' => '19701030 200501 2 001',
        ]);

        foreach ($this->rincian as $r) {
            $perjalanan->rincianBiaya()->create($r);
        }

        return redirect()->route('perjalanan.riwayat');
    }

    public function render()
    {
        $provinsis = Kota::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');
        $kotas = [];
        if ($this->provinsi) {
            $kotas = Kota::where('provinsi', $this->provinsi)->orderBy('nama')->get();
        }

        return view('livewire.perjalanan.form-pengajuan', [
            'provinsis' => $provinsis,
            'kotas' => $kotas, 
            'coas' => Coa::where('aktif', true)->orderBy('kode')->get(),
        ])->layout('layouts.app');
    }
}