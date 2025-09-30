<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPranotaPerbaikanKontainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pembayaran',
        'nomor_cetakan',
        'bank',
        'jenis_transaksi',
        'tanggal_kas',
        'total_pembayaran',
        'penyesuaian',
        'total_setelah_penyesuaian',
        'alasan_penyesuaian',
        'status',
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'total_pembayaran' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_setelah_penyesuaian' => 'decimal:2',
    ];

    /**
     * Get the pranota perbaikan kontainers associated with this pembayaran.
     */
    public function pranotaPerbaikanKontainers()
    {
        return $this->belongsToMany(PranotaPerbaikanKontainer::class, 'pembayaran_pranota_perbaikan_kontainer_items', 'pembayaran_pranota_perbaikan_kontainer_id', 'pranota_perbaikan_kontainer_id')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
