<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalBuruhBatam extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'kontainer_ids',
        'nominal',
        'adjustment',
        'notes_adjustment',
        'total_nominal',
        'nomor_bukti',
        'penerima',
        'nama_vendor',
        'bank_id',
        'nomor_rekening',
    ];

    protected $casts = [
        'kontainer_ids' => 'array',
        'nominal' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'total_nominal' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
