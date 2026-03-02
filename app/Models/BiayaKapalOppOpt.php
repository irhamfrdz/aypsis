<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalOppOpt extends Model
{
    protected $table = 'biaya_kapal_opp_opts';

    protected $fillable = [
        'biaya_kapal_id',
        'pricelist_opp_opt_id',
        'kapal',
        'voyage',
        'jumlah',
        'tarif',
        'subtotal',
        'total_nominal',
        'dp',
        'sisa_pembayaran',
    ];

    protected $casts = [
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

    // Relationship to PricelistOppOpt
    public function pricelistOppOpt()
    {
        return $this->belongsTo(PricelistOppOpt::class, 'pricelist_opp_opt_id');
    }
}
