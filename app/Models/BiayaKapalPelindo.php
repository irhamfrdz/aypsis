<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalPelindo extends Model
{
    protected $fillable = ['biaya_kapal_id', 'nama_kapal', 'no_voyage'];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }
}
