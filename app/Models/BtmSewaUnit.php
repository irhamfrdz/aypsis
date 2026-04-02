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
