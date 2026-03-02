<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistThc extends Model
{
    protected $fillable = [
        'nama_barang',
        'lokasi',
        'vendor',
        'tarif',
        'status',
    ];
}
