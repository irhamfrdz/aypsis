<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoCuti extends Model
{
    protected $guarded = [];

    // Appended attribute for remaining leave
    protected $appends = ['sisa_cuti'];

    public function getSisaCutiAttribute()
    {
        return $this->total_cuti - $this->cuti_terpakai;
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
