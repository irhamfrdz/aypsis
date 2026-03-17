<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaVendor extends Model
{
    protected $table = 'btm_sewa_vendors';

    protected $fillable = [
        'name',
    ];
}
