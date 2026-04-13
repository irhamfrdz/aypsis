<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaLemburItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_lembur_items';

    protected $fillable = [
        'pembayaran_pranota_lembur_id',
        'pranota_lembur_id',
        'subtotal',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
    ];

    public function pembayaranPranotaLembur()
    {
        return $this->belongsTo(PembayaranPranotaLembur::class, 'pembayaran_pranota_lembur_id');
    }

    public function pranotaLembur()
    {
        return $this->belongsTo(PranotaLembur::class, 'pranota_lembur_id');
    }
}
