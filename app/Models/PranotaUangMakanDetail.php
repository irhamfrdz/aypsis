<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaUangMakanDetail extends Model
{
    protected $table = 'pranota_uang_makan_details';

    protected $fillable = [
        'pranota_uang_makan_id',
        'karyawan_id',
        'kehadiran',
        'nominal_awal',
        'adjustment',
        'total_akhir',
        'catatan',
    ];

    public function pranota()
    {
        return $this->belongsTo(PranotaUangMakan::class, 'pranota_uang_makan_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
