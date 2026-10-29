<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TarifHari extends Model
{
    protected $fillable = ['kota_id', 'golongan_id', 'nominal'];
    public function kota() { return $this->belongsTo(Kota::class); }
    public function golongan() { return $this->belongsTo(Golongan::class); }
}