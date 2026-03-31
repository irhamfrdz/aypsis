<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaPranota extends Model
{
    protected $table = 'btm_sewa_pranotas';

    protected $fillable = [
        'nomor',
        'vendor_id',
        'no_invoice',
        'tgl_invoice',
        'total_aypsis',
        'total_vendor_bill',
        'dpp',
        'ppn',
        'pph',
        'grand_total',
        'status',
    ];

    public function vendor()
    {
        return $this->belongsTo(BtmSewaVendor::class, 'vendor_id');
    }

    public function audits()
    {
        return $this->hasMany(BtmSewaAudit::class, 'pranota_id');
    }
}
