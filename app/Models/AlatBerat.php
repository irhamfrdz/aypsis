<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatBerat extends Model
{
    use Auditable, HasFactory;

    protected $table = 'alat_berats';

    protected $fillable = [
        'kode_alat',
        'nama',
        'nickname',
        'warna',
        'jenis',
        'merk',
        'tipe',
        'kapasitas',
        'nomor_seri',
        'nomor_sertifikat_sia',
        'tanggal_terbit_sertifikat_sia',
        'tanggal_kadaluarsa_sertifikat_sia',
        'biaya_sertifikat_sia',
        'tahun_pembuatan',
        'lokasi',
        'tarif_harian',
        'tarif_bulanan',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_terbit_sertifikat_sia' => 'date',
        'tanggal_kadaluarsa_sertifikat_sia' => 'date',
        'biaya_sertifikat_sia' => 'decimal:2',
    ];
}
