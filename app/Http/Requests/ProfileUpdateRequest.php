<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:30', Rule::unique(User::class)->ignore($this->user()->id)],
            'nama_panggilan' => ['nullable', 'string', 'max:50'],
            'pangkat_id' => ['nullable', 'exists:pangkats,id'],
            'golongan_id' => ['nullable', 'exists:golongans,id'],
            'jabatan_id' => ['nullable', 'exists:jabatans,id'],
            'divisi_id' => ['nullable', 'exists:divisis,id'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'ttd_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}