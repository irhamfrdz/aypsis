<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontainerSewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor',
        'tarif',
        'ukuran_kontainer',
        'harga',
        'tanggal_harga_awal',
        'tanggal_harga_akhir',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_harga_awal' => 'date',
        'tanggal_harga_akhir' => 'date',
    ];
}
