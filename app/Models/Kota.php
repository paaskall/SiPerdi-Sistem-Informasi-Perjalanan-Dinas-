<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $fillable = ['nama', 'provinsi'];
    public function tarifHaris() { return $this->hasMany(TarifHari::class); }
}