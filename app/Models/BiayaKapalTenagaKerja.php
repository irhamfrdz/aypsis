<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalTenagaKerja extends Model
{
    protected $table = 'biaya_kapal_tenaga_kerjas';

    protected $fillable = [
        'biaya_kapal_id',
        'buruh_id',
        'nominal',
        'kapal',
        'voyage',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    public function buruh()
    {
        return $this->belongsTo(Buruh::class, 'buruh_id');
    }
}
