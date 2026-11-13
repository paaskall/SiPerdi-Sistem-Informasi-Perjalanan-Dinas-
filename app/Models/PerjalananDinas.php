<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerjalananDinas extends Model
{
    use SoftDeletes;

    protected $table = 'perjalanan_dinas';
    
    protected $fillable = [
        'nomor_pengajuan', 'no_pembukuan', 'akun', 'no_spd',
        'pegawai_id', 'divisi_id', 'kota_tujuan_id', 'tempat_berangkat',
        'alat_angkutan', 'tingkat_biaya',
        'tanggal_berangkat', 'tanggal_pulang', 'jumlah_hari',
        'maksud_perjalanan', 'total_estimasi', 'status',
        'finance_approved_by', 'finance_approved_at', 'catatan_keuangan',
        'printed_at', 'received_by_finance_at', 'received_by_finance_id',
        'nama_bendahara', 'nip_bendahara', 'nama_ppk', 'nip_ppk',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_pulang' => 'date',
        'finance_approved_at' => 'datetime',
        'printed_at' => 'datetime',              // BARU
        'received_by_finance_at' => 'datetime',  // BARU
    ];

    // Tambahkan relasi
    public function penerimaKeuangan() { return $this->belongsTo(User::class, 'received_by_finance_id'); }    public function pegawai() { return $this->belongsTo(User::class, 'pegawai_id'); }
    public function divisi() { return $this->belongsTo(Divisi::class); }
    public function kotaTujuan() { return $this->belongsTo(Kota::class, 'kota_tujuan_id'); }
    public function rincianBiaya() { return $this->hasMany(PerjalananBiayaEstimasi::class, 'perjalanan_id'); }
    public function approver() { return $this->belongsTo(User::class, 'finance_approved_by'); }
    public function laporan()
    {
        return $this->hasOne(LaporanPerjalanan::class, 'perjalanan_id');
    }
}