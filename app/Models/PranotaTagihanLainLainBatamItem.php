<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaTagihanLainLainBatamItem extends Model
{
    protected $fillable = [
        'pranota_id',
        'tagihan_id',
        'total_biaya',
    ];

    protected $casts = [
        'total_biaya' => 'decimal:2',
    ];

    public function pranota()
    {
        return $this->belongsTo(PranotaTagihanLainLainBatam::class, 'pranota_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(TagihanServiceKendaraanBatam::class, 'tagihan_id');
    }
}
