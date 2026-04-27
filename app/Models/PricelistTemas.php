<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistTemas extends Model
{
    protected $table = 'pricelist_temas';

    protected $fillable = [
        'jenis_biaya',
        'lokasi',
        'size',
        'harga',
        'status',
    ];
}
