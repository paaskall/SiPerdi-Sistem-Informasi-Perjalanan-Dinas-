<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanPerjalanan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'perjalanan_id', 'pegawai_id',
        'ringkasan_kegiatan', 'hasil_perjalanan', 'kendala', 'total_biaya_riil',
        'file_lampiran', 'status',
    ];

    public function perjalanan() { return $this->belongsTo(PerjalananDinas::class, 'perjalanan_id'); }
    public function pegawai() { return $this->belongsTo(User::class, 'pegawai_id'); }
}