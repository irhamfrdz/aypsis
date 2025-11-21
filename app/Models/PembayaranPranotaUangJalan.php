<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranPranotaUangJalan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran_pranota_uang_jalans';

    protected $fillable = [
        'pranota_uang_jalan_id',
        'nomor_pembayaran',
        'nomor_accurate',
        'nomor_cetakan',
        'tanggal_pembayaran',
        'bank',
        'jenis_transaksi',
        'total_pembayaran',
        'total_tagihan_penyesuaian',
        'total_tagihan_setelah_penyesuaian',
        'alasan_penyesuaian',
        'keterangan',
        'status_pembayaran',
        'bukti_pembayaran',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'tanggal_pembayaran',
        'deleted_at',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
        'total_pembayaran' => 'decimal:2',
        'total_tagihan_penyesuaian' => 'decimal:2',
        'total_tagihan_setelah_penyesuaian' => 'decimal:2',
    ];

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    // Constants for payment methods
    const METHOD_CASH = 'cash';
    const METHOD_TRANSFER = 'transfer';
    const METHOD_CHECK = 'check';
    const METHOD_GIRO = 'giro';

    /**
     * Relationship with PranotaUangJalan
     */
    public function pranotaUangJalan()
    {
        return $this->belongsTo(PranotaUangJalan::class, 'pranota_uang_jalan_id');
    }

    /**
     * Relationship with User (created_by)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with User (updated_by)
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_pembayaran, 0, ',', '.');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status_pembayaran) {
            case self::STATUS_PENDING:
                return 'Menunggu';
            case self::STATUS_PAID:
                return 'Lunas';
            case self::STATUS_CANCELLED:
                return 'Dibatalkan';
            default:
                return ucfirst($this->status_pembayaran);
        }
    }

    /**
     * Get method label
     */
    public function getMethodLabelAttribute()
    {
        switch ($this->jenis_transaksi) {
            case self::METHOD_CASH:
                return 'Tunai';
            case self::METHOD_TRANSFER:
                return 'Transfer';
            case self::METHOD_CHECK:
                return 'Cek';
            case self::METHOD_GIRO:
                return 'Giro';
            default:
                return ucfirst($this->jenis_transaksi);
        }
    }

    /**
     * Check if payment is paid
     */
    public function isPaid()
    {
        return $this->status_pembayaran === self::STATUS_PAID;
    }

    /**
     * Check if payment is cancelled
     */
    public function isCancelled()
    {
        return $this->status_pembayaran === self::STATUS_CANCELLED;
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status_pembayaran === self::STATUS_PENDING;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status_pembayaran', $status);
        }
        return $query;
    }

    /**
     * Scope for filtering by method
     */
    public function scopeByMethod($query, $method)
    {
        if ($method) {
            return $query->where('jenis_transaksi', $method);
        }
        return $query;
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                  ->orWhere('nomor_cetakan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('pranotaUangJalan', function($subQ) use ($search) {
                      $subQ->where('nomor_pranota', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }

    /**
     * Generate nomor pembayaran
     */
    public static function generateNomorPembayaran()
    {
        $lastRecord = self::withTrashed()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRecord ? 
            intval(substr($lastRecord->nomor_pembayaran, -4)) + 1 : 
            1;

        return 'PUJ-' . now()->format('ym') . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}