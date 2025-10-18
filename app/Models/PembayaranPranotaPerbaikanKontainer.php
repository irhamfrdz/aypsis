<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


use App\Traits\Auditable;
class PembayaranPranotaPerbaikanKontainer extends Model
{
    use HasFactory;

    use Auditable;
    protected $fillable = [
        'pranota_perbaikan_kontainer_id',
        'tanggal_pembayaran',
        'nominal_pembayaran',
        'nomor_invoice',
        'metode_pembayaran',
        'keterangan',
        'status_pembayaran',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'nominal_pembayaran' => 'decimal:2',
    ];

    /**
     * Get the pranota perbaikan kontainer associated with this pembayaran.
     */
    public function pranotaPerbaikanKontainer()
    {
        return $this->belongsTo(PranotaPerbaikanKontainer::class, 'pranota_perbaikan_kontainer_id');
    }

    /**
     * Get the user who created this pembayaran.
     */
    public function pembuatPembayaran()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated this pembayaran.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
