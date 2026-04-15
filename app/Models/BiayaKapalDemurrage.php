<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaKapalDemurrage extends Model
{
    use HasFactory;

    protected $table = 'biaya_kapal_demurrages';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'lokasi',
        'vendor',
        'penerima',
        'rekening',
        'kontainer_ids',
        'subtotal',
        'biaya_materai',
        'ppn',
        'pph',
        'adjustment',
        'notes_adjustment',
        'total_biaya',
    ];

    protected $casts = [
        'kontainer_ids' => 'array',
        'subtotal' => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
