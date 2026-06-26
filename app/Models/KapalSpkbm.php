<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KapalSpkbm extends Model
{
    protected $table = 'kapal_spkbms';

    protected $fillable = [
        'kapal_id',
        'nomor_surat',
        'hal',
        'ditujukan_kepada',
        'voyage',
        'rencana_tiba',
        'rencana_sandar',
        'rencana_bongkar',
        'rencana_muat',
        'tujuan',
    ];

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }
}
