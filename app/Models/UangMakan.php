<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangMakan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
