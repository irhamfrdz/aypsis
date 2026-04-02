<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaRate extends Model
{
    protected $table = 'btm_sewa_rates';

    protected $fillable = [
        'vendor_id',
        'type_id',
        'size_id',
        'monthly_rate',
        'daily_rate',
        'start_date',
    ];

    protected $casts = [
        'monthly_rate' => 'float',
        'daily_rate' => 'float',
        'start_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(BtmSewaVendor::class, 'vendor_id');
    }

    public function type()
    {
        return $this->belongsTo(BtmSewaType::class, 'type_id');
    }

    public function size()
    {
        return $this->belongsTo(BtmSewaSize::class, 'size_id');
    }
}
