<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalPerijinanDetail extends Model
{
    protected $table = 'biaya_kapal_perijinan_details';

    protected $fillable = [
        'biaya_kapal_perijinan_id',
        'pricelist_perijinan_id',
        'nama_perijinan',
        'tarif'
    ];

    public function perijinan()
    {
        return $this->belongsTo(BiayaKapalPerijinan::class, 'biaya_kapal_perijinan_id');
    }

    public function pricelist()
    {
        return $this->belongsTo(PricelistPerijinan::class, 'pricelist_perijinan_id');
    }
}
