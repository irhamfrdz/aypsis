<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalPerlengkapan extends Model
{
    protected $table = 'biaya_kapal_perlengkapan';

    protected $fillable = [
        'biaya_kapal_id',
        'nama_kapal',
        'no_voyage',
        'keterangan',
        'jumlah_biaya',
    ];

    protected $casts = [
        'jumlah_biaya' => 'decimal:2',
    ];

    /**
     * Relationship: belongs to BiayaKapal
     */
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
