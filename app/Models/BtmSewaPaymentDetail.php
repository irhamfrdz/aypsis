<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaPaymentDetail extends Model
{
    protected $table = 'btm_sewa_payment_details';

    protected $fillable = [
        'btm_sewa_payment_id',
        'btm_sewa_pranota_id',
        'subtotal',
    ];

    public function payment()
    {
        return $this->belongsTo(BtmSewaPayment::class, 'btm_sewa_payment_id');
    }

    public function pranota()
    {
        return $this->belongsTo(BtmSewaPranota::class, 'btm_sewa_pranota_id');
    }
}
