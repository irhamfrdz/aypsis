<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalPerijinanKarantina extends Model
{
    protected $table = 'biaya_kapal_perijinan_karantinas';

    protected $fillable = [
        'biaya_kapal_perijinan_id',
        'source_type',
        'source_id',
        'nomor_dokumen',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function perijinan()
    {
        return $this->belongsTo(BiayaKapalPerijinan::class, 'biaya_kapal_perijinan_id');
    }
}
