<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceAtbItem extends Model
{
    protected $fillable = ['uraian', 'jumlah', 'satuan', 'harga_satuan', 'subtotal'];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceAtb::class, 'invoice_atb_id');
    }
}
