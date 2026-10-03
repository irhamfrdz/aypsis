<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaLemburKaryawan extends Model
{
    protected $table = 'pranota_lembur_karyawans';

    protected $fillable = [
        'pranota_lembur_karyawan_header_id',
        'karyawan_id',
        'periode_mulai',
        'periode_selesai',
        'tanggal_lembur',
        'jam_lembur',
        'nominal_awal',
        'adjustment',
        'total_akhir',
        'catatan',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'tanggal_lembur' => 'array',
        'nominal_awal' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'total_akhir' => 'decimal:2',
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
