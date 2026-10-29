<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reimbursement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'perjalanan_id', 'nomor_reimb', 'total_realisasi', 'selisih', 'status',
        'finance_approved_by', 'finance_approved_at', 'catatan_keuangan',
        'pimpinan_signed_by', 'pimpinan_signed_at'
    ];

    public function perjalanan() { return $this->belongsTo(PerjalananDinas::class, 'perjalanan_id'); }
    public function items() { return $this->hasMany(ReimbursementItem::class, 'reimbursement_id'); }
    public function approver() { return $this->belongsTo(User::class, 'finance_approved_by'); }
    public function pimpinan() { return $this->belongsTo(User::class, 'pimpinan_signed_by'); }
}