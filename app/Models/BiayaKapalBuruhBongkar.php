<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalBuruhBongkar extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'kontainer_ids',
        'nominal',
        'adjustment',
        'notes_adjustment',
        'pph_percent',
        'pph_amount',
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
        'pph_percent' => 'decimal:2',
        'pph_amount' => 'decimal:2',
        'total_nominal' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

}
