<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaKontainerItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_kontainer_items';

    protected $fillable = [
        'pembayaran_pranota_kontainer_id',
        'pranota_id',
        'amount',
        'keterangan'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    /**
     * Relationship to main payment
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranPranotaKontainer::class, 'pembayaran_pranota_kontainer_id');
    }

    /**
     * Relationship to pranota
     */
    public function pranota()
    {
        return $this->belongsTo(PranotaTagihanKontainerSewa::class, 'pranota_id');
    }
}
