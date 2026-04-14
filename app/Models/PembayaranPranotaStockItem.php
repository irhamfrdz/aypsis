<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaStockItem extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_stock_items';

    protected $fillable = [
        'pembayaran_pranota_stock_id',
        'pranota_stock_id',
        'subtotal',
    ];

    public function pembayaranPranotaStock()
    {
        return $this->belongsTo(PembayaranPranotaStock::class, 'pembayaran_pranota_stock_id');
    }

    public function pranotaStock()
    {
        return $this->belongsTo(PranotaStock::class, 'pranota_stock_id');
    }
}
