<?php

namespace App\Livewire\MasterData;

use App\Livewire\Concerns\ResetsPageOnSearch;
use App\Livewire\Concerns\WithRoleLayout;
use App\Livewire\Concerns\WithSwal;
use App\Livewire\Forms\UserForm;
use App\Models\Divisi;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Pangkat;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserIndex extends Component
{
    use WithPagination, WithRoleLayout, WithSwal, ResetsPageOnSearch;

    public string $search = '';
    public bool $isModalOpen = false;

    public UserForm $form;

    // =========================================================
    // ACTIONS
    // =========================================================

    public function openModal(): void
    {
        $this->form->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->form->resetForm();
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->form->setUser($user);
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $isEdit = (bool) $this->form->userId;

        $this->form->save();

        $this->swalSuccess(
            $isEdit ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.'
        );

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            $this->swalError('Anda tidak bisa menghapus akun sendiri.');
            return;
        }

        User::findOrFail($id)->delete();
        $this->swalSuccess('User berhasil dihapus.');
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render()
    {
        $users = User::with(['divisi', 'jabatan', 'golongan', 'pangkat'])
            ->when($this->search !== '', function ($q) {
                $s = $this->search;
                $q->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                        ->orWhere('nip', 'like', "%{$s}%")
                        ->orWhere('username', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                });
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
        ])->layout($this->resolveLayout());
    }
}