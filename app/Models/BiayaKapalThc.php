<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalThc extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'vendor',
        'tanda_terima_ids',
        'subtotal',
        'pph',
        'total_biaya',
    ];

    protected $casts = [
        'tanda_terima_ids' => 'array',
        'subtotal'         => 'decimal:2',
        'pph'              => 'decimal:2',
        'total_biaya'      => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }
}
