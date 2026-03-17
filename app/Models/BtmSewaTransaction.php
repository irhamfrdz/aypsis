<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaTransaction extends Model
{
    protected $table = 'btm_sewa_transactions';

    protected $fillable = [
        'unit_number',
        'date_in',
        'date_out',
        'billing_mode',
    ];

    protected $casts = [
        'date_in' => 'date',
        'date_out' => 'date',
    ];
}
