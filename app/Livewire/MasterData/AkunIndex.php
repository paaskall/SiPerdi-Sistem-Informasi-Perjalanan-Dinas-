<?php

namespace App\Livewire\MasterData;

use App\Models\Akun;
use Livewire\Component;
use Livewire\WithPagination;

class AkunIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $isModalOpen = false;
    public $akunId = null;

    public $kode = '';
    public $nama = '';
    public $aktif = true;

    protected function rules()
    {
        return [
            'kode' => 'required|string|max:50|unique:akuns,kode,' . ($this->akunId ?? 'NULL'),
            'nama' => 'required|string|max:255',
            'aktif' => 'boolean',
        ];
    }

    protected $messages = [
        'kode.required' => 'Kode akun wajib diisi.',
        'kode.unique' => 'Kode akun sudah digunakan.',
        'nama.required' => 'Nama akun wajib diisi.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->akunId = null;
        $this->kode = '';
        $this->nama = '';
        $this->aktif = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $akun = Akun::findOrFail($id);
        $this->akunId = $akun->id;
        $this->kode = $akun->kode;
        $this->nama = $akun->nama;
        $this->aktif = (bool) $akun->aktif;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->akunId) {
            $akun = Akun::findOrFail($this->akunId);
            $akun->update([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'aktif' => $this->aktif,
            ]);
            session()->flash('message', 'Akun berhasil diperbarui.');
        } else {
            Akun::create([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'aktif' => $this->aktif,
            ]);
            session()->flash('message', 'Akun berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $akun = Akun::findOrFail($id);

        // Cegah hapus jika masih dipakai di perjalanan dinas
        $dipakai = \App\Models\PerjalananDinas::where('akun', $akun->kode)->exists();
        if ($dipakai) {
            session()->flash('message', 'Akun tidak bisa dihapus karena sudah dipakai di perjalanan dinas. Nonaktifkan saja.');
            return;
        }

        $akun->delete();
        session()->flash('message', 'Akun berhasil dihapus.');
    }

    public function render()
    {
        $akuns = Akun::query()
            ->when($this->search, function ($q) {
                $q->where('kode', 'like', '%' . $this->search . '%')
                  ->orWhere('nama', 'like', '%' . $this->search . '%');
            })
            ->orderBy('kode')
            ->paginate(10);

        return view('livewire.master-data.akun-index', [
            'akuns' => $akuns,
        ])->layout(auth()->user()->hasRole('super-admin') ? 'layouts.super-admin' : 'layouts.app');
    }
}