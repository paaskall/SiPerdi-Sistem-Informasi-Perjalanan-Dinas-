<?php

namespace App\Livewire\MasterData;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\Pangkat;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserIndex extends Component
{
    use WithPagination;

    public $userId, $name, $nip, $username, $email, $password, $password_confirmation;
    public $divisi_id, $jabatan_id, $golongan_id, $pangkat_id, $role;
    public $isModalOpen = false;
    public $search = '';

    protected function rules()
    {
        return [
            // Hanya Username & Password yang wajib
            'username' => 'required|string|max:50|unique:users,username,' . $this->userId,
            'password' => $this->userId ? 'nullable|string|min:6|confirmed' : 'required|string|min:6|confirmed',
            
            // Field lain opsional
            'name' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:30|unique:users,nip,' . $this->userId,
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->userId,
            'divisi_id' => 'nullable|exists:divisis,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'golongan_id' => 'nullable|exists:golongans,id',
            'pangkat_id' => 'nullable|exists:pangkats,id',
            'role' => 'nullable|exists:roles,name',
        ];
    }

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $users = User::with(['divisi', 'jabatan', 'golongan', 'pangkat'])
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master-data.user-index', [
            'users' => $users,
            'divisis' => Divisi::orderBy('nama')->get(),
            'jabatans' => Jabatan::orderBy('level')->get(),
            'golongans' => Golongan::orderBy('nama')->get(),
            'pangkats' => Pangkat::orderBy('nama')->get(),
            'roles' => Role::all(),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->resetFields(); $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; $this->resetFields(); }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->nip = $user->nip;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->divisi_id = $user->divisi_id;
        $this->jabatan_id = $user->jabatan_id;
        $this->golongan_id = $user->golongan_id;
        $this->pangkat_id = $user->pangkat_id;
        $this->role = $user->getRoleNames()->first();
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        // Placeholder untuk field wajib di database yang tidak diisi
        $name = $this->name ?: $this->username;
        $nip = $this->nip ?: '-';
        $email = $this->email ?: $this->username . '@siperdi.local';

        $data = [
            'name' => $name,
            'nip' => $nip,
            'username' => $this->username,
            'email' => $email,
            'divisi_id' => $this->divisi_id,
            'jabatan_id' => $this->jabatan_id,
            'golongan_id' => $this->golongan_id,
            'pangkat_id' => $this->pangkat_id,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // Assign role hanya jika dipilih
        if ($this->role) {
            $user->syncRoles([$this->role]);
        }

        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => $this->userId ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.',
            'icon' => 'success',
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        if ($id == auth()->id()) {
            $this->dispatch('swal', ['title' => 'Gagal!', 'text' => 'Anda tidak bisa menghapus akun sendiri.', 'icon' => 'error']);
            return;
        }
        User::findOrFail($id)->delete();
        $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'User berhasil dihapus.', 'icon' => 'success']);
    }

    private function resetFields()
    {
        $this->reset(['userId', 'name', 'nip', 'username', 'email', 'password', 'password_confirmation', 'divisi_id', 'jabatan_id', 'golongan_id', 'pangkat_id', 'role']);
        $this->resetValidation();
    }
}