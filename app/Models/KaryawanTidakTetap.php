<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaryawanTidakTetap extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'nama_panggilan',
        'divisi',
        'pekerjaan',
        'cabang',
        'nik_ktp',
        'jenis_kelamin',
        'agama',
        'rt_rw',
        'alamat_lengkap',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'email',
        'tanggal_masuk',
        'status_pajak',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];
}
