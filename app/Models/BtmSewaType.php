<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaType extends Model
{
    protected $table = 'btm_sewa_types';

    protected $fillable = [
        'name',
    ];
}
