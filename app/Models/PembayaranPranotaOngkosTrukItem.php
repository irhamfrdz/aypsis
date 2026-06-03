<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaOngkosTrukItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_ongkos_truk_items';

    protected $fillable = [
        'pembayaran_pranota_ongkos_truk_id',
        'pranota_ongkos_truk_id',
        'subtotal',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the payment record
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranPranotaOngkosTruk::class, 'pembayaran_pranota_ongkos_truk_id');
    }

    /**
     * Get the pranota record
     */
    public function pranota()
    {
        return $this->belongsTo(PranotaOngkosTruk::class, 'pranota_ongkos_truk_id');
    }
}
