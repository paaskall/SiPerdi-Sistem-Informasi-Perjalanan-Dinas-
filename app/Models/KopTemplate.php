<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KopTemplate extends Model
{
    protected $fillable = ['nama', 'tipe', 'gambar_kop_path', 'html_template', 'divisi_id', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];
    public function divisi() { return $this->belongsTo(Divisi::class); }
}