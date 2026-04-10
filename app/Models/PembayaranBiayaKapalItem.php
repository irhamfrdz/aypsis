<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranBiayaKapalItem extends Model
{
    protected $table = 'pembayaran_biaya_kapal_items';

    protected $fillable = [
        'pembayaran_biaya_kapal_id',
        'biaya_kapal_id',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(PembayaranBiayaKapal::class, 'pembayaran_biaya_kapal_id');
    }

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
