<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranPranotaLembur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran_pranota_lemburs';

    protected $fillable = [
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

    /**
     * Many-to-many relationship with PranotaLembur through pivot table
     */
    public function pranotaLemburs()
    {
        return $this->belongsToMany(
            PranotaLembur::class,
            'pembayaran_pranota_lembur_items',
            'pembayaran_pranota_lembur_id',
            'pranota_lembur_id'
        )->withPivot('subtotal')->withTimestamps();
    }

    /**
     * Get all items (pivot records)
     */
    public function items()
    {
        return $this->hasMany(PembayaranPranotaLemburItem::class, 'pembayaran_pranota_lembur_id');
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
}
