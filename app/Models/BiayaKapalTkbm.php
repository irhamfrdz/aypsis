<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalTkbm extends Model
{
    protected $table = 'biaya_kapal_tkbm';

    protected $fillable = [
        'biaya_kapal_id',
        'pricelist_tkbm_id',
        'kapal',
        'voyage',
        'no_referensi',
        'jumlah',
        'tarif',
        'subtotal',
        'total_nominal',
        'dp',
        'sisa_pembayaran',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tarif' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_nominal' => 'decimal:2',
        'dp' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
    ];

    // Relationship to BiayaKapal
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    // Relationship to PricelistTkbm
    public function pricelistTkbm()
    {
        return $this->belongsTo(PricelistTkbm::class, 'pricelist_tkbm_id');
    }
}
