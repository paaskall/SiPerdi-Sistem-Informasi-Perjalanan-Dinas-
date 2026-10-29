<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlafonBiaya extends Model
{
    protected $fillable = ['golongan_id', 'komponen', 'nominal'];
    public function golongan() { return $this->belongsTo(Golongan::class); }
}