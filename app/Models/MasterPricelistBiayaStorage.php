<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPricelistBiayaStorage extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor',
        'lokasi',
        'size_kontainer',
        'tarif_massa_1',
        'tarif_massa_2',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tarif_massa_1' => 'decimal:2',
        'tarif_massa_2' => 'decimal:2',
    ];
}
