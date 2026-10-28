<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use Auditable;

    protected $fillable = [
        'nomor_po', 'tanggal_po', 'tanggal_dibutuhkan', 'vendor', 'alamat_vendor',
        'status', 'catatan', 'subtotal', 'diskon', 'pajak_persen', 'pajak', 'total',
        'created_by', 'updated_by',
        'permohonan_amprahan_id',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'tanggal_dibutuhkan' => 'date',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak_persen' => 'decimal:2',
        'pajak' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class)->orderBy('urutan');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function permohonanAmprahan(): BelongsTo
    {
        return $this->belongsTo(PermohonanAmprahan::class);
    }
}
