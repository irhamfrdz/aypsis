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
        return $this->morphTo('karyawan', 'tipe_karyawan', 'karyawan_id');
    }
}
