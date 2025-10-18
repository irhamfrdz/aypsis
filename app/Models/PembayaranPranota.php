<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PembayaranPranota extends Model
{
    use HasFactory, Auditable;

    use Auditable;
    protected $table = 'pembayaran_pranota';

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
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tanggal_kas' => 'date',
        'total_pembayaran' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'total_setelah_penyesuaian' => 'decimal:2'
    ];

    /**
     * Relationship to pranota items through pivot table
     */
    public function pranotas()
    {
        return $this->belongsToMany(
            Pranota::class,
            'pembayaran_pranota_items',
            'pembayaran_pranota_id',
            'pranota_id'
        )->withPivot('amount')->withTimestamps();
    }

    /**
     * Get all payment items
     */
    public function items()
    {
        return $this->hasMany(PembayaranPranotaItem::class);
    }

    /**
     * Calculate total amount from items
     */
    public function calculateTotalFromItems()
    {
        return $this->items()->sum('amount');
    }

    /**
     * Update total amounts
     */
    public function updateTotals()
    {
        $this->total_pembayaran = $this->calculateTotalFromItems();
        $this->total_setelah_penyesuaian = $this->total_pembayaran + $this->penyesuaian;
        $this->save();
    }
}
