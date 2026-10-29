<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerjalananBiayaEstimasi extends Model
{
    // TAMBAHKAN BARIS INI:
    protected $table = 'perjalanan_biaya_estimasi';

    protected $fillable = ['perjalanan_id', 'coa_id', 'keterangan', 'nominal'];

    public function perjalanan() 
    { 
        return $this->belongsTo(PerjalananDinas::class, 'perjalanan_id'); 
    }
    
    public function coa() 
    { 
        return $this->belongsTo(Coa::class, 'coa_id'); 
    }
}