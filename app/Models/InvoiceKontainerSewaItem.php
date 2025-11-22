<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceKontainerSewaItem extends Model
{
    protected $table = 'invoice_kontainer_sewa_items';

    protected $fillable = [
        'invoice_id',
        'tagihan_id',
        'jumlah',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    /**
     * Relasi ke Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceKontainerSewa::class, 'invoice_id');
    }

    /**
     * Relasi ke Tagihan Kontainer
     */
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(DaftarTagihanKontainerSewa::class, 'tagihan_id');
    }
}
