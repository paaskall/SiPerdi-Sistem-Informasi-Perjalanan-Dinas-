<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = ['nama', 'kode'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}