<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalTanggalBayar extends Model
{
    protected $table = 'biaya_kapal_tanggal_bayars';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'tanggal_bayar',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
