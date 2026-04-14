<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPranotaRitItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_rit_items';

    protected $fillable = [
        'pembayaran_pranota_rit_id',
        'pranota_uang_rit_id',
        'subtotal',
    ];

    public function pembayaranHeader(): BelongsTo
    {
        return $this->belongsTo(PembayaranPranotaRit::class, 'pembayaran_pranota_rit_id');
    }

    public function pranotaUangRit(): BelongsTo
    {
        return $this->belongsTo(PranotaUangRit::class, 'pranota_uang_rit_id');
    }
}
