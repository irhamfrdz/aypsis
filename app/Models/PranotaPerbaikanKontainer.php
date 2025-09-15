<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PranotaPerbaikanKontainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'perbaikan_kontainer_id',
        'tanggal_pranota',
        'deskripsi_pekerjaan',
        'nama_teknisi',
        'estimasi_biaya',
        'estimasi_waktu',
        'catatan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'estimasi_biaya' => 'decimal:2',
    ];

    /**
     * Get the perbaikan kontainer that owns the pranota.
     */
    public function perbaikanKontainer(): BelongsTo
    {
        return $this->belongsTo(PerbaikanKontainer::class);
    }

    /**
     * Get the user who created the pranota.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the pranota.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the pembayaran records for this pranota.
     */
    public function pembayaranPranotaPerbaikanKontainers()
    {
        return $this->hasMany(PembayaranPranotaPerbaikanKontainer::class, 'pranota_perbaikan_kontainer_id');
    }
}
