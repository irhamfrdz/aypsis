<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalOperasional extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'keterangan',
        'nominal',
        'total_nominal',
        'dp',
        'sisa_pembayaran',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }

    public function items()
    {
        return $this->hasMany(BiayaKapalOperasionalItem::class);
    }

    public function deleteAllItems()
    {
        return $this->items()->delete();
    }
}
