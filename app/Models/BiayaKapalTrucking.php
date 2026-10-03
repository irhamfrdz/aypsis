<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalTrucking extends Model
{
    protected $table = 'biaya_kapal_trucking';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'nama_vendor',
        'no_bl',
        'container_adjustments',
        'total_biaya_20ft',
        'total_biaya_40ft',
        'subtotal',
        'pph',
        'pph_percent',
        'adjustment',
        'notes_adjustment',
        'total_biaya',
    ];

    protected $casts = [
        'no_bl' => 'array',
        'container_adjustments' => 'array',
        'total_biaya_20ft' => 'decimal:2',
        'total_biaya_40ft' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'pph' => 'decimal:2',
        'pph_percent' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    // Relationship to BiayaKapal
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
