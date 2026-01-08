<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistBiayaDokumen extends Model
{
    use HasFactory;

    protected $table = 'pricelist_biaya_dokumen';

    protected $fillable = [
        'nama_vendor',
        'biaya',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];
}
