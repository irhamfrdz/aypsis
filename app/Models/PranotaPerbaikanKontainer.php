<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PranotaPerbaikanKontainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'deskripsi_pekerjaan',
        'nama_teknisi',
        'total_biaya',
        'catatan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_biaya' => 'decimal:2',
    ];

    /**
     * Get the perbaikan kontainers associated with this pranota.
     */
    public function perbaikanKontainers(): BelongsToMany
    {
        return $this->belongsToMany(PerbaikanKontainer::class, 'pranota_perbaikan_kontainer_items')
                    ->withPivot('biaya_item', 'catatan_item')
                    ->withTimestamps();
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
