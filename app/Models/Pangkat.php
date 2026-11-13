<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    protected $fillable = ['nama', 'golongan_ruang'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}