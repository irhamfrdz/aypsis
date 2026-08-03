<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangLembur extends Model
{
    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'tipe_hari',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'nominal_uang',
        'status',
        'keterangan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
