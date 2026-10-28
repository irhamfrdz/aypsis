<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['urutan', 'deskripsi', 'kuantitas', 'satuan', 'harga_satuan', 'jumlah', 'masuk_asset'];

    protected $casts = [
        'kuantitas' => 'decimal:3',
        'harga_satuan' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'masuk_asset' => 'boolean',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
