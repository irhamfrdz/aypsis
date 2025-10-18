<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_items';

    protected $fillable = [
        'pembayaran_pranota_id',
        'pranota_id',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    /**
     * Relationship to PembayaranPranota
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranPranota::class, 'pembayaran_pranota_id');
    }

    /**
     * Relationship to Pranota
     */
    public function pranota()
    {
        return $this->belongsTo(Pranota::class, 'pranota_id');
    }
}
