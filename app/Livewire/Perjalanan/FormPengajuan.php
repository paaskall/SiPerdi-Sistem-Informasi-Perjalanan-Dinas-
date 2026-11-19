<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\Kota;
use App\Models\Coa;
use App\Models\Akun;
use App\Models\TarifHari;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Golongan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class FormPengajuan extends Component
{
    /* =========================================================
       PROPERTIES
       ========================================================= */
    public $provinsi; 
    public $kota_tujuan_id, $tanggal_berangkat, $tanggal_pulang, $jumlah_hari = 0, $maksud_perjalanan;
    public $tempat_berangkat, $alat_angkutan = 'Pesawat', $tingkat_biaya = 'C';
    public $no_pembukuan, $akun, $no_spd;
    public $rincian = [];
    public $total_estimasi = 0;

    public $profilLengkap = false;
    public $missing = [];

    // Super Admin Properties
    public $isSuperAdmin = false;
    public $pegawai_id_pilihan = ''; 
    public $filterDivisi = '';
    public $filterJabatan = '';
    public $searchPegawai = '';

    // Manual Input Properties
    public $is_pegawai_manual = false;
    public $nama_pegawai_manual = '';
    public $nip_pegawai_manual = '';
    public $golongan_id_manual = '';
    public $divisi_id_manual = '';

    /* =========================================================
       LIFECYCLE HOOKS
       ========================================================= */
    public function mount()
    {
        $user = auth()->user();
        $this->isSuperAdmin = $user->hasRole('super-admin');

        if ($this->isSuperAdmin) {
            $this->pegawai_id_pilihan = $user->id;
        }

        $this->profilLengkap = $user->profilLengkap();
        $this->missing = $user->dataProfilKurang();

        $editId = request()->query('edit');

        if ($editId) {
            $this->loadEditData($editId);
        } else {
            $this->generateAutoNumbers();
            $this->tambahBaris();
        }
    }

    /* =========================================================
       LOAD DATA (EDIT MODE)
       ========================================================= */
    private function loadEditData($editId)
    {
        $user = auth()->user();
        $perjalanan = PerjalananDinas::with(['rincianBiaya', 'kotaTujuan'])->findOrFail($editId);

        if ($perjalanan->pegawai_id !== $user->id && !$this->isSuperAdmin) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit form ini.');
        }
        if ($perjalanan->status !== 'DRAFT') {
            abort(403, 'Form sudah diproses, tidak bisa diedit.');
        }

        $this->pegawai_id_pilihan = $perjalanan->pegawai_id;
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
            $this->jumlah_hari = \Carbon\Carbon::parse($this->tanggal_berangkat)->diffInDays(\Carbon\Carbon::parse($this->tanggal_pulang)) + 1;
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
    }

    /* =========================================================
       AUTO GENERATE NUMBERS
       ========================================================= */
    private function generateAutoNumbers()
    {
        $this->tempat_berangkat = 'Makassar';
        $count = PerjalananDinas::whereYear('created_at', date('Y'))->count() + 1;
        $this->no_pembukuan = 'PB/' . date('Y') . '/' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Default akun: ambil akun aktif pertama dari tabel akuns
        $this->akun = Akun::where('aktif', true)->orderBy('kode')->value('kode')
            ?? '086.01.CO.4825.ABQ.001.051.A';

        $this->no_spd = (2866 + $count) . '/PLB.1.2.1/SDM.04.1';
    }

    /* =========================================================
       WATCHERS (LIVEWIRE HOOKS)
       ========================================================= */
    public function updatedFilterDivisi() { $this->pegawai_id_pilihan = ''; }
    public function updatedFilterJabatan() { $this->pegawai_id_pilihan = ''; }
    public function updatedSearchPegawai() { $this->pegawai_id_pilihan = ''; }

    public function updatedIsPegawaiManual()
    {
        if ($this->is_pegawai_manual) {
            $this->pegawai_id_pilihan = '';
        }
    }

    public function updatedPegawaiIdPilihan()
    {
        if ($this->isSuperAdmin && $this->pegawai_id_pilihan) {
            $pegawai = User::find($this->pegawai_id_pilihan);
            if ($pegawai) {
                $this->profilLengkap = $pegawai->profilLengkap();
                $this->missing = $pegawai->dataProfilKurang();
            }
        }
    }

    public function updatedProvinsi()
    {
        $this->kota_tujuan_id = null;
    }

    public function updatedKotaTujuanId()
    {
        if (!$this->kota_tujuan_id) return;
        $this->isiTarifHarian();
    }

    public function updated($property)
    {
        if (in_array($property, ['tanggal_berangkat', 'tanggal_pulang'])) {
            $this->hitungJumlahHari();
        }

        if (str_contains($property, 'rincian.') && str_ends_with($property, '.coa_id')) {
            $this->prosesAutoSync($property);
        }

        if (str_contains($property, 'rincian')) {
            $this->hitungTotal();
        }
    }

    /* =========================================================
       CORE LOGIC: AUTO SYNC & TARIF
       ========================================================= */
    private function hitungJumlahHari()
    {
        if ($this->tanggal_berangkat && $this->tanggal_pulang) {
            $start = \Carbon\Carbon::parse($this->tanggal_berangkat);
            $end = \Carbon\Carbon::parse($this->tanggal_pulang);
            $this->jumlah_hari = $start->diffInDays($end) + 1;
        } else {
            $this->jumlah_hari = 0;
        }
    }

    private function prosesAutoSync($property)
    {
        $parts = explode('.', $property);
        $index = (int) $parts[1];

        if (empty($this->rincian[$index]['coa_id'])) return;

        $coa = Coa::find($this->rincian[$index]['coa_id']);
        $namaCoa = $coa ? strtolower($coa->nama) : '';

        // Penginapan
        if (str_contains($namaCoa, 'penginapan') || str_contains($namaCoa, 'hotel') || str_contains($namaCoa, 'akomodasi')) {
            $this->rincian[$index]['qty'] = max(1, $this->jumlah_hari - 1);
            $this->rincian[$index]['satuan'] = 'malam';
        } 
        // Uang Harian
        elseif (str_contains($namaCoa, 'uang harian') || str_contains($namaCoa, 'uang saku') || str_contains($namaCoa, 'harian')) {
            $this->rincian[$index]['qty'] = $this->jumlah_hari;
            $this->rincian[$index]['satuan'] = 'OH';
            
            if ($this->kota_tujuan_id) {
                $this->isiTarifHarian($index);
            }
        }

        if (empty($this->rincian[$index]['harga_satuan'])) {
            $this->rincian[$index]['harga_satuan'] = 0;
        }
    }

    private function isiTarifHarian($specificIndex = null)
    {
        $golonganId = null;
        if ($this->isSuperAdmin && $this->pegawai_id_pilihan) {
            $pegawai = User::find($this->pegawai_id_pilihan);
            $golonganId = $pegawai->golongan_id ?? null;
        } else {
            $golonganId = auth()->user()->golongan_id;
        }

        if (!$golonganId) return;

        $tarif = TarifHari::where('kota_id', $this->kota_tujuan_id)
            ->where('golongan_id', $golonganId)
            ->first();

        if ($tarif) {
            foreach ($this->rincian as $index => $item) {
                if ($specificIndex !== null && $index !== $specificIndex) continue;

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

    /* =========================================================
       RINCIAN BIAYA ACTIONS
       ========================================================= */
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

    /* =========================================================
       SAVE ACTION
       ========================================================= */
    public function simpan()
    {
        // 1. Validasi Pegawai
        if ($this->isSuperAdmin && !$this->is_pegawai_manual && empty($this->pegawai_id_pilihan)) {
            $this->addError('pegawai_id_pilihan', 'Pilih pegawai terlebih dahulu.');
            return;
        }

        // 2. Validasi Form (rincian, tanggal, dll)
        $this->validateForm();

        // 3. Tentukan Pegawai
        if ($this->isSuperAdmin && $this->is_pegawai_manual) {
            $pegawai = $this->createManualPegawai();
            if (!$pegawai) return; // Gagal buat pegawai (duplikat NIP, dll)
        } else {
            $pegawai = $this->isSuperAdmin ? User::find($this->pegawai_id_pilihan) : auth()->user();
            
            // Cek profil lengkap (khusus non-manual)
            if (!$pegawai->profilLengkap()) {
                $this->addError('profil', 'Data profil pegawai tersebut belum lengkap.');
                return;
            }
        }

        // 4. Simpan Perjalanan & Rincian
        $perjalanan = $this->savePerjalanan($pegawai);
        $this->saveRincian($perjalanan);

        return redirect()->route('perjalanan.riwayat');
    }

    private function validateForm()
    {
        $this->validate([
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
        ]);
    }

    private function createManualPegawai()
    {
        $this->validate([
            'nama_pegawai_manual' => 'required|string|max:255',
            'golongan_id_manual' => 'required|exists:golongans,id',
            'divisi_id_manual' => 'nullable|exists:divisis,id',
            'nip_pegawai_manual' => 'nullable|string|max:30|unique:users,nip',
        ], [
            'nama_pegawai_manual.required' => 'Nama pegawai wajib diisi.',
            'golongan_id_manual.required' => 'Golongan wajib dipilih.',
            'nip_pegawai_manual.unique' => 'NIP ini sudah terdaftar di sistem.',
        ]);

        $username = $this->nip_pegawai_manual ?: Str::slug($this->nama_pegawai_manual) . rand(100,999);
        
        // Cek duplikat username
        if (User::where('username', $username)->exists()) {
            $username .= rand(100,999);
        }

        try {
            $pegawai = User::create([
                'name' => $this->nama_pegawai_manual,
                'nip' => $this->nip_pegawai_manual ?: null,
                'username' => $username,
                'email' => $username . '@siperdi.local',
                'password' => Hash::make('password123'),
                'golongan_id' => $this->golongan_id_manual,
                'divisi_id' => $this->divisi_id_manual ?: null,
            ]);
            
            $pegawai->assignRole('pegawai');
            
            // Paksa profil lengkap = true agar UMK bisa disimpan
            $this->pegawai_id_pilihan = $pegawai->id;
            $this->profilLengkap = true;

            return $pegawai;
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangkap error database
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Gagal Membuat Pegawai',
                'text' => 'Terjadi kesalahan database: ' . $e->getMessage(),
            ]);
            return null;
        }
    }

    private function savePerjalanan($pegawai)
    {
        $editId = request()->query('edit');

        if ($editId) {
            $perjalanan = PerjalananDinas::findOrFail($editId);
            $perjalanan->update($this->getPerjalananData($pegawai));
        } else {
            $perjalanan = PerjalananDinas::create(array_merge($this->getPerjalananData($pegawai), [
                'nomor_pengajuan' => str_pad(PerjalananDinas::whereYear('created_at', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT) . '/UMK/' . date('Y'),
                'no_pembukuan' => $this->no_pembukuan,
                'akun' => $this->akun,
                'no_spd' => $this->no_spd,
                'status' => 'DRAFT',
                'nama_bendahara' => 'Suharna Sudirman, S.Kom',
                'nip_bendahara' => '19810303 200902 2 006',
                'nama_ppk' => 'Sudarmi Narwis, SE., MM',
                'nip_ppk' => '19701030 200501 2 001',
            ]));
        }

        return $perjalanan;
    }

    private function getPerjalananData($pegawai)
    {
        return [
            'pegawai_id' => $pegawai->id,
            'divisi_id' => $pegawai->divisi_id,
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

    private function saveRincian($perjalanan)
    {
        $editId = request()->query('edit');

        if ($editId) {
            $perjalanan->rincianBiaya()->delete();
        }

        foreach ($this->rincian as $r) {
            $perjalanan->rincianBiaya()->create($r);
        }
    }

    /* =========================================================
       RENDER
       ========================================================= */
    public function render()
    {
        $provinsis = Kota::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');
        $kotas = $this->provinsi ? Kota::where('provinsi', $this->provinsi)->orderBy('nama')->get() : [];

        $pegawais = [];
        $divisis = [];
        $jabatans = [];
        $golongans = [];
        
        if ($this->isSuperAdmin) {
            $divisis = Divisi::orderBy('nama')->get();
            $jabatans = Jabatan::orderBy('nama')->get();
            $golongans = Golongan::orderBy('nama')->get();
            
            $queryPegawai = User::orderBy('name');
            if ($this->filterDivisi) $queryPegawai->where('divisi_id', $this->filterDivisi);
            if ($this->filterJabatan) $queryPegawai->where('jabatan_id', $this->filterJabatan);
            if ($this->searchPegawai) {
                $queryPegawai->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchPegawai . '%')
                      ->orWhere('nip', 'like', '%' . $this->searchPegawai . '%');
                });
            }
            $pegawais = $queryPegawai->get();
        }

        // ✅ Tentukan Pemohon untuk Data Diri (manual atau list)
        if ($this->isSuperAdmin) {
            if ($this->is_pegawai_manual && $this->nama_pegawai_manual) {
                // Input manual: buat objek User sementara
                $pemohon = new User([
                    'name' => $this->nama_pegawai_manual,
                    'nip' => $this->nip_pegawai_manual,
                    'golongan_id' => $this->golongan_id_manual,
                    'divisi_id' => $this->divisi_id_manual,
                ]);
                $pemohon->setRelation('golongan', Golongan::find($this->golongan_id_manual));
                $pemohon->setRelation('divisi', Divisi::find($this->divisi_id_manual));
            } elseif ($this->pegawai_id_pilihan) {
                $pemohon = User::with(['golongan', 'divisi', 'jabatan', 'pangkat'])->find($this->pegawai_id_pilihan);
            } else {
                $pemohon = auth()->user();
            }
        } else {
            $pemohon = auth()->user();
        }

        return view('livewire.perjalanan.form-pengajuan', [
            'provinsis' => $provinsis,
            'kotas' => $kotas, 
            'coas' => Coa::where('aktif', true)->orderBy('kode')->get(),
            'akuns' => Akun::where('aktif', true)->orderBy('kode')->get(),
            'pegawais' => $pegawais,
            'divisis' => $divisis,
            'jabatans' => $jabatans,
            'golongans' => $golongans,
            'isSuperAdmin' => $this->isSuperAdmin,
            'pemohon' => $pemohon,
        ])->layout(auth()->user()->hasRole('super-admin') ? 'layouts.super-admin' : 'layouts.app');
    }
}