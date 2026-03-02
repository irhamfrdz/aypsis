<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistOppOpt extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_barang',
        'vendor',
        'lokasi',
        'tarif',
        'status',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
    ];
}
