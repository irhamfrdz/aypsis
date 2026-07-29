<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalBuruhBongkarDetail extends Model
{
    protected $fillable = [
        'biaya_kapal_buruh_bongkar_id',
        'manifest_id',
        'surat_jalan_tipe',
    ];

    public function buruhBongkar()
    {
        return $this->belongsTo(BiayaKapalBuruhBongkar::class, 'biaya_kapal_buruh_bongkar_id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id');
    }
}
