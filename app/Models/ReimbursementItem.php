<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReimbursementItem extends Model
{
    protected $fillable = ['reimbursement_id', 'coa_id', 'keterangan', 'nominal', 'file_struk'];

    public function reimbursement() { return $this->belongsTo(Reimbursement::class); }
    public function coa() { return $this->belongsTo(Coa::class); }
}