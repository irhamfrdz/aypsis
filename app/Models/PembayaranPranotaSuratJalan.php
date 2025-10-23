<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranPranotaSuratJalan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran_pranota_surat_jalan';

    protected $fillable = [
        'pranota_surat_jalan_id',
        'nomor_pembayaran',
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
    const STATUS_PARTIAL = 'partial';
    const STATUS_CANCELLED = 'cancelled';

    // Constants for payment methods
    const METHOD_CASH = 'cash';
    const METHOD_TRANSFER = 'transfer';
    const METHOD_CHECK = 'check';
    const METHOD_GIRO = 'giro';

    /**
     * Get the pranota surat jalan that owns the payment.
     */
    public function pranotaSuratJalan()
    {
        return $this->belongsTo(PranotaSuratJalan::class, 'pranota_surat_jalan_id');
    }

    /**
     * Get the user who created the payment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the payment.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pembayaran', $status);
    }

    /**
     * Scope for filtering by payment method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('metode_pembayaran', $method);
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pembayaran', [$startDate, $endDate]);
    }

    /**
     * Get available payment statuses.
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_PARTIAL => 'Sebagian',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    /**
     * Get available payment methods.
     */
    public static function getPaymentMethods()
    {
        return [
            self::METHOD_CASH => 'Tunai',
            self::METHOD_TRANSFER => 'Transfer Bank',
            self::METHOD_CHECK => 'Cek',
            self::METHOD_GIRO => 'Giro',
        ];
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status_pembayaran] ?? $this->status_pembayaran;
    }

    /**
     * Get payment method label - placeholder for future implementation.
     */
    public function getMethodLabelAttribute()
    {
        // Since metode_pembayaran field was removed, return a default or bank name
        return $this->bank ?? 'Transfer Bank';
    }

    /**
     * Get formatted payment amount.
     */
    public function getFormattedAmountAttribute()
    {
        $amount = $this->total_tagihan_setelah_penyesuaian ?? $this->total_pembayaran ?? 0;
        return 'Rp ' . number_format((float) $amount, 2, ',', '.');
    }

    /**
     * Check if payment is fully paid.
     */
    public function isPaid()
    {
        return $this->status_pembayaran === self::STATUS_PAID;
    }

    /**
     * Check if payment is pending.
     */
    public function isPending()
    {
        return $this->status_pembayaran === self::STATUS_PENDING;
    }

    /**
     * Check if payment is cancelled.
     */
    public function isCancelled()
    {
        return $this->status_pembayaran === self::STATUS_CANCELLED;
    }
}
