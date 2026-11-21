<?php

namespace App\Livewire\Perjalanan;

use App\Actions\Perjalanan\CreateManualPegawaiAction;
use App\Actions\Perjalanan\SimpanPerjalananAction;
use App\Livewire\Forms\PerjalananForm;
use App\Models\Akun;
use App\Models\Coa;
use App\Models\Divisi;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Kota;
use App\Models\PerjalananDinas;
use App\Models\User;
use App\Services\NomorDokumenService;
use App\Services\TarifService;
use Livewire\Component;

class FormPengajuan extends Component
{
    public PerjalananForm $form;

    // Filter super admin
    public string $filterDivisi = '';
    public string $filterJabatan = '';
    public string $searchPegawai = '';

    public bool $profilLengkap = false;
    public array $missing = [];

    public function mount(NomorDokumenService $nomorService): void
    {
        $user = auth()->user();
        $this->form->isSuperAdmin = $user->hasRole('super-admin');

        if ($this->form->isSuperAdmin) {
            $this->form->pegawai_id_pilihan = $user->id;
        }

        $this->profilLengkap = $user->profilLengkap();
        $this->missing = $user->dataProfilKurang();

        $editId = request()->query('edit');

        if ($editId) {
            $this->loadEditData((int) $editId);
        } else {
            $this->initializeNew();
            $this->applyNomorDokumen($nomorService->generate());
            $this->tambahBaris();
        }
    }

    private function initializeNew(): void
    {
        $this->form->tempat_berangkat = 'Makassar';
    }

    private function applyNomorDokumen(array $nomor): void
    {
        $this->form->no_pembukuan = $nomor['no_pembukuan'];
        $this->form->akun = $nomor['akun'];
        $this->form->no_spd = $nomor['no_spd'];
    }

    private function loadEditData(int $editId): void
    {
        $user = auth()->user();
        $perjalanan = PerjalananDinas::with(['rincianBiaya', 'kotaTujuan'])->findOrFail($editId);

        if ($perjalanan->pegawai_id !== $user->id && ! $this->form->isSuperAdmin) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit form ini.');
        }
        if ($perjalanan->status !== 'DRAFT') {
            abort(403, 'Form sudah diproses, tidak bisa diedit.');
        }

        $this->form->pegawai_id_pilihan = $perjalanan->pegawai_id;
        $this->form->setPerjalanan($perjalanan);
    }

    // =========================================================
    // WATCHERS
    // =========================================================

    public function updatedFilterDivisi(): void
    {
        $this->form->pegawai_id_pilihan = null;
    }

    public function updatedFilterJabatan(): void
    {
        $this->form->pegawai_id_pilihan = null;
    }

    public function updatedSearchPegawai(): void
    {
        $this->form->pegawai_id_pilihan = null;
    }

    public function updatedFormIsPegawaiManual(): void
    {
        if ($this->form->is_pegawai_manual) {
            $this->form->pegawai_id_pilihan = null;
        }
    }

    public function updatedFormPegawaiIdPilihan(): void
    {
        if ($this->form->isSuperAdmin && $this->form->pegawai_id_pilihan) {
            $pegawai = User::find($this->form->pegawai_id_pilihan);
            if ($pegawai) {
                $this->profilLengkap = $pegawai->profilLengkap();
                $this->missing = $pegawai->dataProfilKurang();
            }
        }
    }

    public function updatedFormProvinsi(): void
    {
        $this->form->kota_tujuan_id = null;
    }

    public function updatedFormKotaTujuanId(TarifService $tarifService): void
    {
        if (! $this->form->kota_tujuan_id) {
            return;
        }

        $golonganId = $tarifService->resolveGolonganId(
            $this->form->isSuperAdmin,
            $this->form->pegawai_id_pilihan,
        );

        $this->form->rincian = $tarifService->fillTarifHarianForAll(
            $this->form->rincian,
            $this->form->kota_tujuan_id,
            $golonganId,
        );
        $this->hitungTotal();
    }

    public function updatedFormTanggalBerangkat(): void
    {
        $this->hitungJumlahHari();
        $this->hitungTotal();
    }

    public function updatedFormTanggalPulang(): void
    {
        $this->hitungJumlahHari();
        $this->hitungTotal();
    }

    // =========================================================
    // ACTIONS
    // =========================================================

    /**
     * Dipanggil manual dari blade via wire:change saat Kategori (COA) diubah.
     */
    public function syncRincian(int $index): void
    {
        $golonganId = app(TarifService::class)->resolveGolonganId(
            $this->form->isSuperAdmin,
            $this->form->pegawai_id_pilihan,
        );

        $this->form->rincian[$index] = app(TarifService::class)->syncRincianItem(
            $this->form->rincian[$index],
            $this->form->jumlah_hari,
            $this->form->kota_tujuan_id,
            $golonganId,
        );

        $this->hitungTotal();
    }

    private function hitungJumlahHari(): void
    {
        if ($this->form->tanggal_berangkat && $this->form->tanggal_pulang) {
            $start = \Carbon\Carbon::parse($this->form->tanggal_berangkat);
            $end = \Carbon\Carbon::parse($this->form->tanggal_pulang);
            $this->form->jumlah_hari = $start->diffInDays($end) + 1;
        } else {
            $this->form->jumlah_hari = 0;
        }
    }

    public function tambahBaris(): void
    {
        $this->form->rincian[] = [
            'coa_id' => '',
            'keterangan' => '',
            'qty' => 1,
            'satuan' => 'kali',
            'harga_satuan' => 0,
            'nominal' => 0,
        ];
    }

    public function hapusBaris(int $index): void
    {
        unset($this->form->rincian[$index]);
        $this->form->rincian = array_values($this->form->rincian);
        $this->hitungTotal();
    }

    public function hitungTotal(): void
    {
        $this->form->rincian = app(TarifService::class)->recalculateNominal($this->form->rincian);
        $this->form->total_estimasi = app(TarifService::class)->hitungTotal($this->form->rincian);
    }

    public function simpan(
        SimpanPerjalananAction $simpanAction,
        CreateManualPegawaiAction $createPegawaiAction,
    ) {
        $this->form->validate();

        // Tentukan pegawai
        if ($this->form->isSuperAdmin && $this->form->is_pegawai_manual) {
            $this->form->validate(
                $this->form->manualRules(),
                $this->form->manualMessages(),
            );

            $pegawai = $createPegawaiAction->execute(
                $this->form->nama_pegawai_manual,
                $this->form->nip_pegawai_manual,
                $this->form->golongan_id_manual,
                $this->form->divisi_id_manual,
            );
            $this->form->pegawai_id_pilihan = $pegawai->id;
            $this->profilLengkap = true;
        } else {
            $pegawai = $this->form->isSuperAdmin
                ? User::find($this->form->pegawai_id_pilihan)
                : auth()->user();

            if (! $pegawai->profilLengkap()) {
                $this->addError('profil', 'Data profil pegawai tersebut belum lengkap.');
                return;
            }
        }

        $editId = request()->query('edit');

        $simpanAction->execute(
            pegawai: $pegawai,
            perjalananData: $this->form->toPerjalananData(),
            rincian: $this->form->rincian,
            editId: $editId ? (int) $editId : null,
            nomorDokumen: $this->form->toNomorDokumenData(),
        );

        return redirect()->route('perjalanan.riwayat');
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render()
    {
        $provinsis = Kota::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');
        $kotas = $this->form->provinsi
            ? Kota::where('provinsi', $this->form->provinsi)->orderBy('nama')->get()
            : [];

        $pegawais = [];
        $divisis = [];
        $jabatans = [];
        $golongans = [];

        if ($this->form->isSuperAdmin) {
            $divisis = Divisi::orderBy('nama')->get();
            $jabatans = Jabatan::orderBy('nama')->get();
            $golongans = Golongan::orderBy('nama')->get();

            $queryPegawai = User::orderBy('name');
            if ($this->filterDivisi) {
                $queryPegawai->where('divisi_id', $this->filterDivisi);
            }
            if ($this->filterJabatan) {
                $queryPegawai->where('jabatan_id', $this->filterJabatan);
            }
            if ($this->searchPegawai) {
                $q = $this->searchPegawai;
                $queryPegawai->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('nip', 'like', "%{$q}%");
                });
            }
            $pegawais = $queryPegawai->get();
        }

        $pemohon = $this->resolvePemohon();

        return view('livewire.perjalanan.form-pengajuan', [
            'provinsis' => $provinsis,
            'kotas' => $kotas,
            'coas' => Coa::where('aktif', true)->orderBy('kode')->get(),
            'akuns' => Akun::where('aktif', true)->orderBy('kode')->get(),
            'pegawais' => $pegawais,
            'divisis' => $divisis,
            'jabatans' => $jabatans,
            'golongans' => $golongans,
            'isSuperAdmin' => $this->form->isSuperAdmin,
            'pemohon' => $pemohon,
        ])->layout(auth()->user()->hasRole('super-admin') ? 'layouts.super-admin' : 'layouts.app');
    }

    private function resolvePemohon(): User
    {
        if (! $this->form->isSuperAdmin) {
            return auth()->user();
        }

        if ($this->form->is_pegawai_manual && $this->form->nama_pegawai_manual) {
            $pemohon = new User([
                'name' => $this->form->nama_pegawai_manual,
                'nip' => $this->form->nip_pegawai_manual,
                'golongan_id' => $this->form->golongan_id_manual,
                'divisi_id' => $this->form->divisi_id_manual,
            ]);
            $pemohon->setRelation('golongan', Golongan::find($this->form->golongan_id_manual));
            $pemohon->setRelation('divisi', Divisi::find($this->form->divisi_id_manual));

            return $pemohon;
        }

        if ($this->form->pegawai_id_pilihan) {
            return User::with(['golongan', 'divisi', 'jabatan', 'pangkat'])
                ->find($this->form->pegawai_id_pilihan);
        }

        return auth()->user();
    }
}