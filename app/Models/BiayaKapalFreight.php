<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalFreight extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'vendor',
        'kontainer_ids',
        'subtotal',
        'biaya_meterai',
        'pph',
        'total_biaya',
    ];

    protected $casts = [
        'kontainer_ids' => 'array',
        'subtotal'      => 'decimal:2',
        'biaya_meterai' => 'decimal:2',
        'pph'           => 'decimal:2',
        'total_biaya'   => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }
}
