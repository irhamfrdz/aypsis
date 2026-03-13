<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaKapalPerijinan extends Model
{
    use HasFactory;

    protected $table = 'biaya_kapal_perijinan';

    protected $fillable = [
        'biaya_kapal_id',
        'nama_perijinan',
        'nama_kapal',
        'no_voyage',
        'keterangan',
        'jumlah_biaya'
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
