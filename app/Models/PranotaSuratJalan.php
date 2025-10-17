<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaSuratJalan extends Model
{
    use HasFactory;

    protected $table = 'pranota_surat_jalans';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'periode_tagihan',
        'jumlah_surat_jalan',
        'total_amount',
        'status',
        'catatan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Relationship dengan SuratJalan (many-to-many)
     */
    public function suratJalans()
    {
        return $this->belongsToMany(SuratJalan::class, 'pranota_surat_jalan_items', 'pranota_surat_jalan_id', 'surat_jalan_id');
    }

    /**
     * Get first surat jalan for backward compatibility
     */
    public function getFirstSuratJalan()
    {
        return $this->suratJalans()->first();
    }

    /**
     * Relationship dengan User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk status pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk status paid
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope untuk periode tertentu
     */
    public function scopeByPeriode($query, $periode)
    {
        return $query->where('periode_tagihan', $periode);
    }

    /**
     * Scope untuk bulan tertentu
     */
    public function scopeByMonth($query, $month, $year = null)
    {
        $year = $year ?: date('Y');
        return $query->whereMonth('tanggal_pranota', $month)
                    ->whereYear('tanggal_pranota', $year);
    }

    /**
     * Accessor untuk formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Accessor untuk formatted tanggal pranota
     */
    public function getFormattedTanggalPranotaAttribute()
    {
        return $this->tanggal_pranota ? $this->tanggal_pranota->format('d/m/Y') : '-';
    }

    /**
     * Accessor untuk status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Accessor untuk status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }
}
