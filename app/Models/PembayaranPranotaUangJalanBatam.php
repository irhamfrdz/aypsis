<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranPranotaUangJalanBatam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran_pranota_uang_jalan_batams';

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
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Many-to-many relationship with PranotaUangJalanBatam through pivot table
     */
    public function pranotaUangJalanBatams()
    {
        return $this->belongsToMany(
            PranotaUangJalanBatam::class,
            'pembayaran_pranota_uang_jalan_batam_items',
            'pembayaran_pranota_uang_jalan_btm_id',
            'pranota_uang_jalan_batam_id'
        )->withPivot('subtotal')->withTimestamps();
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
     * Get first pranota as model instance (for attributes access)
     */
    public function getPranotaUangJalanBatamAttribute()
    {
        return $this->pranotaUangJalanBatams->first();
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
}
