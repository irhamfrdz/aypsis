<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalLolo extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'lokasi',
        'vendor',
        'kontainer_ids',
        'subtotal',
        'biaya_materai',
        'ppn',
        'pph',
        'total_biaya',
    ];

    protected $casts = [
        'kontainer_ids' => 'array',
        'subtotal'      => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'ppn'           => 'decimal:2',
        'pph'           => 'decimal:2',
        'total_biaya'   => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }
}
