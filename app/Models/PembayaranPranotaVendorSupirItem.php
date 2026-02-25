<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaVendorSupirItem extends Model
{
    protected $fillable = [
        'pembayaran_id',
        'pranota_id',
        'nominal',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(PembayaranPranotaVendorSupir::class, 'pembayaran_id');
    }

    public function pranota()
    {
        return $this->belongsTo(PranotaInvoiceVendorSupir::class, 'pranota_id');
    }
}
