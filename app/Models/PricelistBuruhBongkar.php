<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class PricelistBuruhBongkar extends Model
{
    use SoftDeletes;

    protected $table = 'pricelist_buruh_bongkars';

    protected $fillable = [
        'size',
        'lokasi',
        'nominal',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'status' => 'boolean',
    ];
}
