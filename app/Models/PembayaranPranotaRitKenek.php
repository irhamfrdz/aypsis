<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PembayaranPranotaRitKenek extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pranota_rit_keneks';

    protected $fillable = [
        'nomor_pembayaran',
        'nomor_accurate',
        'tanggal_pembayaran',
        'bank',
        'jenis_transaksi',
        'total_pembayaran',
        'total_tagihan_penyesuaian',
        'total_tagihan_setelah_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'status_pembayaran',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'total_pembayaran' => 'decimal:2',
        'total_tagihan_penyesuaian' => 'decimal:2',
        'total_tagihan_setelah_penyesuaian' => 'decimal:2',
    ];

    public function pranotaUangRitKeneks(): BelongsToMany
    {
        return $this->belongsToMany(
            PranotaUangRitKenek::class,
            'pembayaran_pranota_rit_kenek_items',
            'pembayaran_pranota_rit_kenek_id',
            'pranota_uang_rit_kenek_id'
        )->withPivot('subtotal')->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
