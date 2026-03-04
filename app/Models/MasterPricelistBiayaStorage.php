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
        'biaya_per_hari',
        'free_time',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'biaya_per_hari' => 'decimal:2',
        'free_time' => 'integer',
    ];
}
