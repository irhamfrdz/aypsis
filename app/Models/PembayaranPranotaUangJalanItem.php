<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaUangJalanItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_uang_jalan_items';

    protected $fillable = [
        'pembayaran_pranota_uang_jalan_id',
        'pranota_uang_jalan_id',
        'subtotal',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the pembayaran that owns the item.
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranPranotaUangJalan::class, 'pembayaran_pranota_uang_jalan_id');
    }

    /**
     * Get the pranota uang jalan for this item.
     */
    public function pranotaUangJalan()
    {
        return $this->belongsTo(PranotaUangJalan::class, 'pranota_uang_jalan_id');
    }
}
