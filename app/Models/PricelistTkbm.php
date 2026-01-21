<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistTkbm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_barang',
        'tarif',
        'status',
    ];

    // Optional: Casts for decimals if you want to ensure they are returned as floats/decimals
    protected $casts = [
        'tarif' => 'decimal:2',
    ];
}
