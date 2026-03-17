<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaUnit extends Model
{
    protected $table = 'btm_sewa_units';

    protected $fillable = [
        'unit_number',
        'vendor_id',
        'type_id',
        'size_id',
    ];
}
