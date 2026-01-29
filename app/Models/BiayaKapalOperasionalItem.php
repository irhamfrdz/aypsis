<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalOperasionalItem extends Model
{
    protected $fillable = [
        'biaya_kapal_operasional_id',
        'deskripsi',
        'nominal',
    ];

    public function operasional()
    {
        return $this->belongsTo(BiayaKapalOperasional::class, 'biaya_kapal_operasional_id');
    }
}
