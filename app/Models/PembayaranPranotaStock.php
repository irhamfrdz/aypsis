<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranPranotaStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran_pranota_stocks';

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
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Many-to-many relationship with PranotaStock through pivot table
     */
    public function pranotaStocks()
    {
        return $this->belongsToMany(
            PranotaStock::class,
            'pembayaran_pranota_stock_items',
            'pembayaran_pranota_stock_id',
            'pranota_stock_id'
        )->withPivot('subtotal')->withTimestamps();
    }

    /**
     * Get all items (pivot records)
     */
    public function items()
    {
        return $this->hasMany(PembayaranPranotaStockItem::class, 'pembayaran_pranota_stock_id');
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
}
