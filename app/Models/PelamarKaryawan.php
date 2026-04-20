<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelamarKaryawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'wearpack_size',
        'no_safety_shoes',
        'nomor_rekening',
        'npwp',
        'no_nik',
        'no_kartu_keluarga',
        'no_bpjs_kesehatan',
        'no_ketenagakerjaan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_handphone',
        'tanggungan_anak',
        'alamat_lengkap',
        'kelurahan',
        'kecamatan',
        'kota_kabupaten',
        'provinsi',
        'kode_pos',
        'email',
        'kontak_darurat',
        'cv_path',
        'status'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
}
