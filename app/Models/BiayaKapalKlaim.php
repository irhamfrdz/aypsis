<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaKapalKlaim extends Model
{
    use HasFactory;

    protected $table = 'biaya_kapal_klaims';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'vendor',
        'penerima',
        'kontainer_ids',
        'subtotal',
        'total_biaya',
        'keterangan',
    ];

    protected $casts = [
        'kontainer_ids' => 'array',
        'subtotal' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
