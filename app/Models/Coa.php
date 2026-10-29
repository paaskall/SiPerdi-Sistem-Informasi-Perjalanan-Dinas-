<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $fillable = ['kode', 'nama', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];
}