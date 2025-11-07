<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PranotaUangJalan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pranota_uang_jalans';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'periode_tagihan',
        'jumlah_uang_jalan',
        'total_amount',
        'penyesuaian',
        'keterangan_penyesuaian',
        'status_pembayaran',
        'catatan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_amount' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Status pembayaran constants
     */
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the uang jalans associated with this pranota.
     */
    public function uangJalans()
    {
        return $this->belongsToMany(UangJalan::class, 'pranota_uang_jalan_items', 'pranota_uang_jalan_id', 'uang_jalan_id')
                    ->withTimestamps();
    }

    /**
     * Get the pembayaran associated with this pranota.
     */
    public function pembayaranPranotaUangJalan()
    {
        return $this->hasOne(PembayaranPranotaUangJalan::class, 'pranota_uang_jalan_id');
    }

    /**
     * Get the user who created this pranota.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this pranota.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status_pembayaran', $status);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriode($query, $periode)
    {
        if ($periode) {
            return $query->where('periode_tagihan', $periode);
        }
        return $query;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status_pembayaran) {
            case self::STATUS_PAID:
                return 'bg-green-100 text-green-800';
            case self::STATUS_CANCELLED:
                return 'bg-red-100 text-red-800';
            case self::STATUS_UNPAID:
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get formatted status text
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status_pembayaran) {
            case self::STATUS_PAID:
                return 'Lunas';
            case self::STATUS_CANCELLED:
                return 'Dibatalkan';
            case self::STATUS_UNPAID:
            default:
                return 'Belum Dibayar';
        }
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Get formatted penyesuaian amount
     */
    public function getFormattedPenyesuaianAttribute()
    {
        return 'Rp ' . number_format($this->penyesuaian, 0, ',', '.');
    }

    /**
     * Get total amount setelah penyesuaian
     */
    public function getTotalWithPenyesuaianAttribute()
    {
        return $this->total_amount + $this->penyesuaian;
    }

    /**
     * Get formatted total amount dengan penyesuaian
     */
    public function getFormattedTotalWithPenyesuaianAttribute()
    {
        return 'Rp ' . number_format($this->total_with_penyesuaian, 0, ',', '.');
    }

    /**
     * Get total amount for payment (used in payment forms)
     */
    public function getTotalForPaymentAttribute()
    {
        return $this->total_with_penyesuaian;
    }
}