<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistMeratus extends Model
{
    protected $table = 'pricelist_meratus';

    protected $fillable = [
        'jenis_biaya',
        'lokasi',
        'size',
        'harga',
        'status',
    ];
}
