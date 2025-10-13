<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TujuanKegiatanUtama extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'cabang',
        'wilayah',
        'dari',
        'ke',
        'uang_jalan_20ft',
        'uang_jalan_40ft',
        'keterangan',
        'liter',
        'jarak_dari_penjaringan_km',
        'mel_20ft',
        'mel_40ft',
        'ongkos_truk_20ft',
        'ongkos_truk_40ft',
        'antar_lokasi_20ft',
        'antar_lokasi_40ft',
    ];
}