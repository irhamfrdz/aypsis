<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPranotaPerbaikanKontainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'pranota_perbaikan_kontainer_id',
        'tanggal_pembayaran',
        'nominal_pembayaran',
        'nomor_invoice',
        'metode_pembayaran',
        'keterangan',
        'status_pembayaran',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'nominal_pembayaran' => 'decimal:2',
    ];

    /**
     * Get the pranota perbaikan kontainer that owns the pembayaran.
     */
    public function pranotaPerbaikanKontainer(): BelongsTo
    {
        return $this->belongsTo(PranotaPerbaikanKontainer::class);
    }

    /**
     * Get the user who created the pembayaran.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the pembayaran.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
