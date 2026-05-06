<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaPermohonanTransferDetail extends Model
{
    protected $table = 'btm_sewa_permohonan_transfer_details';

    protected $fillable = [
        'permohonan_id',
        'btm_sewa_pranota_id',
        'subtotal',
    ];

    public function permohonan()
    {
        return $this->belongsTo(BtmSewaPermohonanTransfer::class, 'permohonan_id');
    }

    public function pranota()
    {
        return $this->belongsTo(BtmSewaPranota::class, 'btm_sewa_pranota_id');
    }
}
