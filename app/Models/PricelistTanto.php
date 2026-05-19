<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistTanto extends Model
{
    protected $table = 'pricelist_tanto';

    protected $fillable = [
        'jenis_biaya',
        'lokasi',
        'size',
        'harga',
        'status',
    ];
}
