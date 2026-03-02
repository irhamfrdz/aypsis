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
        'kontainer_ids',
        'subtotal',
        'biaya_dokumen_muat',
        'biaya_dokumen_bongkar',
        'biaya_materai',
        'pph',
        'total_biaya',
    ];

    protected $casts = [
        'tanda_terima_ids'    => 'array',
        'kontainer_ids'       => 'array',
        'subtotal'            => 'decimal:2',
        'biaya_dokumen_muat'  => 'decimal:2',
        'biaya_dokumen_bongkar' => 'decimal:2',
        'biaya_materai'       => 'decimal:2',
        'pph'                 => 'decimal:2',
        'total_biaya'         => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }
}
