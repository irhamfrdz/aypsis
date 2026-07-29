<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalBuruhBongkar extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'nama_pengirim',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    public function details()
    {
        return $this->hasMany(BiayaKapalBuruhBongkarDetail::class, 'biaya_kapal_buruh_bongkar_id');
    }
}
