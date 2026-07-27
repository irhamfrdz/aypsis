<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalDokumen extends Model
{
    protected $table = 'biaya_kapal_dokumens';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'nomor_bl',
        'vendor_id',
        'nominal',
        'pph',
        'total_biaya'
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    public function setNomorBlAttribute($value)
    {
        $this->attributes['nomor_bl'] = is_array($value) ? implode(', ', $value) : $value;
    }

    public function getNomorBlArrayAttribute()
    {
        if (empty($this->nomor_bl)) {
            return [];
        }
        return array_map('trim', explode(',', $this->nomor_bl));
    }
}
