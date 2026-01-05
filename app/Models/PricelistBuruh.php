<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricelistBuruh extends Model
{
    use SoftDeletes;

    protected $table = 'pricelist_buruh';

    protected $fillable = [
        'barang',
        'size',
        'tipe',
        'tarif',
        'is_active',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
