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
        'subtotal',
        'pph',
        'total_biaya',
    ];

    protected $casts = [
        'no_bl' => 'array',
    ];

    // Relationship to BiayaKapal
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
