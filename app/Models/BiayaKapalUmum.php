<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalUmum extends Model
{
    protected $table = 'biaya_kapal_umums';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'nama_vendor',
        'penerima',
        'nomor_rekening',
        'bank_id',
        'keterangan',
        'nominal',
        'pph',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'pph' => 'decimal:2',
    ];

    /**
     * Relationship: belongs to BiayaKapal
     */
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    /**
     * Relationship: belongs to Bank
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
