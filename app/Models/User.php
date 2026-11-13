<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name', 'nama_panggilan', 'nip', 'username', 'email', 'password',
        'sso_id', 'auth_provider',
        'divisi_id', 'jabatan_id', 'golongan_id', 'pangkat_id',
        'ttd_path', 'aktif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_id');
    }

    /**
     * Cek apakah profil user sudah lengkap untuk pembuatan UMK.
     */
    public function profilLengkap(): bool
    {
        return !empty($this->nip) 
            && !empty($this->pangkat_id) 
            && !empty($this->golongan_id) 
            && !empty($this->jabatan_id) 
            && !empty($this->divisi_id);
    }

    /**
     * Mendapatkan list field yang belum lengkap.
     */
    public function dataProfilKurang(): array
    {
        $kurang = [];
        if (empty($this->nip)) $kurang[] = 'NIP';
        if (empty($this->pangkat_id)) $kurang[] = 'Pangkat';
        if (empty($this->golongan_id)) $kurang[] = 'Golongan';
        if (empty($this->jabatan_id)) $kurang[] = 'Jabatan';
        if (empty($this->divisi_id)) $kurang[] = 'Divisi';
        return $kurang;
    }

    /** Login pakai NIP / email / username */
    public function findForPassport(string $username): ?self
    {
        return static::where('nip', $username)
            ->orWhere('email', $username)
            ->orWhere('username', $username)
            ->first();
    }
}