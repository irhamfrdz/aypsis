<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceKasPinang extends Model
{
    protected $fillable = [
        'nomor_invoice', 'tanggal_transaksi', 'arus_kas', 'akun_kas_id', 'akun_lawan_id', 'pihak_terkait',
        'referensi', 'total', 'status', 'catatan', 'created_by',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceKasPinangItem::class)->orderBy('id');
    }

    public function akunKas(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'akun_kas_id');
    }

    public function akunLawan(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'akun_lawan_id');
    }
}
