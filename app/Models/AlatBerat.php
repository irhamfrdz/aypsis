<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class AlatBerat extends Model
{
    use HasFactory, Auditable;

    protected $table = 'alat_berats';

    protected $fillable = [
        'kode_alat',
        'nama',
        'warna',
        'jenis',
        'merk',
        'tipe',
        'kapasitas',
        'nomor_seri',
        'tahun_pembuatan',
        'lokasi',
        'keterangan',
        'status',
    ];

    protected $casts = [
        
    ];
}
