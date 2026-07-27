<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalDokumen extends Model
{
    protected $table = 'biaya_kapal_dokumens';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'nomor_bl',
        'vendor_id',
        'nominal',
        'pph',
        'total_biaya'
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
