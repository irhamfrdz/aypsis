<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaUangJalanBongkaranItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_uang_jalan_bongkaran_items';

    protected $fillable = [
        'pembayaran_pranota_uang_jalan_bongkaran_id',
        'pranota_uang_jalan_bongkaran_id',
        'subtotal'
    ];

    public function pranotaUangJalanBongkaran()
    {
        return $this->belongsTo(PranotaUangJalanBongkaran::class, 'pranota_uang_jalan_bongkaran_id');
    }

    public function pembayaran()
    {
        return $this->belongsTo(PembayaranPranotaUangJalanBongkaran::class, 'pembayaran_pranota_uang_jalan_bongkaran_id');
    }
}
