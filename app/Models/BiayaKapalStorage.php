<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaKapalStorage extends Model
{
    use HasFactory;

    protected $table = 'biaya_kapal_storages';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'lokasi',
        'vendor',
        'kontainer_ids',
        'subtotal',
        'biaya_materai',
        'ppn',
        'pph',
        'adjustment',
        'notes_adjustment',
        'payment_mode',
        'dp_storage_id',
        'nilai_tagihan',
        'nominal_dibayar',
        'sisa_pembayaran',
        'total_biaya',
    ];

    protected $casts = [
        'kontainer_ids' => 'array',
        'subtotal' => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'nilai_tagihan' => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    public function dpStorage()
    {
        return $this->belongsTo(self::class, 'dp_storage_id');
    }

    public function pelunasanDetails()
    {
        return $this->hasMany(self::class, 'dp_storage_id');
    }
}
