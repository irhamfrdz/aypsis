<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaLemburKaryawan extends Model
{
    protected $table = 'pranota_lembur_karyawans';

    protected $fillable = [
        'pranota_lembur_karyawan_header_id',
        'karyawan_id',
        'jam_lembur',
        'nominal_awal',
        'adjustment',
        'total_akhir',
        'catatan',
    ];

    public function pranotaLemburKaryawanHeader()
    {
        return $this->belongsTo(PranotaLemburKaryawanHeader::class, 'pranota_lembur_karyawan_header_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
