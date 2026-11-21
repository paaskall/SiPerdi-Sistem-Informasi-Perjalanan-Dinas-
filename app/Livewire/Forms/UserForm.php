<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?int $userId = null;
    public string $name = '';
    public string $nip = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $divisi_id = null;
    public ?int $jabatan_id = null;
    public ?int $golongan_id = null;
    public ?int $pangkat_id = null;
    public ?string $role = null;

    // =========================================================
    // ATURAN VALIDASI
    // =========================================================

    public function rules(): array
    {
        return [
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($this->userId),
            ],
            'password' => $this->userId
                ? 'nullable|string|min:6|confirmed'
                : 'required|string|min:6|confirmed',
            'name' => 'nullable|string|max:255',
            'nip' => [
                'nullable', 'string', 'max:30',
                Rule::unique('users', 'nip')->ignore($this->userId),
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'divisi_id' => 'nullable|exists:divisis,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'golongan_id' => 'nullable|exists:golongans,id',
            'pangkat_id' => 'nullable|exists:pangkats,id',
            'role' => 'nullable|exists:roles,name',
        ];
    }

    // =========================================================
    // LOAD FROM MODEL
    // =========================================================

    public function setUser(User $user): void
    {
        $this->userId = $user->id;
        $this->name = $user->name ?? '';
        $this->nip = $user->nip ?? '';
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->divisi_id = $user->divisi_id;
        $this->jabatan_id = $user->jabatan_id;
        $this->golongan_id = $user->golongan_id;
        $this->pangkat_id = $user->pangkat_id;
        $this->role = $user->getRoleNames()->first();
        $this->password = '';
        $this->password_confirmation = '';
    }

    // =========================================================
    // SAVE
    // =========================================================

    public function save(): User
    {
        $this->validate();

        // Placeholder untuk field wajib di DB yang tidak diisi
        $data = [
            'name' => $this->name ?: $this->username,
            'nip' => $this->nip ?: '-',
            'username' => $this->username,
            'email' => $this->email ?: $this->username . '@siperdi.local',
            'divisi_id' => $this->divisi_id,
            'jabatan_id' => $this->jabatan_id,
            'golongan_id' => $this->golongan_id,
            'pangkat_id' => $this->pangkat_id,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        if ($this->role) {
            $user->syncRoles([$this->role]);
        }

        return $user;
    }

    // =========================================================
    // RESET
    // =========================================================

    public function resetForm(): void
    {
        $this->reset();
        $this->resetValidation();
    }
}