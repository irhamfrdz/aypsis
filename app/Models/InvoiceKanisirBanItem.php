<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceKanisirBanItem extends Model
{
    protected $fillable = [
        'invoice_kanisir_ban_id',
        'stock_ban_id',
        'harga',
    ];

    public function invoice()
    {
        return $this->belongsTo(InvoiceKanisirBan::class, 'invoice_kanisir_ban_id');
    }

    public function stockBan()
    {
        return $this->belongsTo(StockBan::class, 'stock_ban_id');
    }
}
