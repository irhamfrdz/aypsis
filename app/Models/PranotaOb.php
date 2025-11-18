<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class PranotaOb extends Model
{
    protected $table = 'pranota_ob';
    
    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'keterangan',
        'total_biaya',
        'penyesuaian',
        'grand_total',
        'status',
        'periode',
        'created_by',
        'approved_at',
        'approved_by'
    ];
    
    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_biaya' => 'decimal:2',
        'penyesuaian' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'approved_at' => 'datetime'
    ];
    
    // Generate nomor pranota otomatis
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->nomor_pranota)) {
                $model->nomor_pranota = static::generateNomorPranota();
            }
        });
    }
    
    /**
     * Relasi ke items pranota
     */
    public function items(): HasMany
    {
        return $this->hasMany(PranotaObItem::class);
    }
    
    /**
     * Relasi ke user yang membuat pranota
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Relasi ke user yang menyetujui pranota
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Generate nomor pranota otomatis
     */
    public static function generateNomorPranota(): string
    {
        $prefix = 'POB';
        $date = now()->format('ymd');
        $lastNumber = static::whereDate('created_at', today())
            ->where('nomor_pranota', 'like', "{$prefix}/{$date}/%")
            ->count();
        
        $sequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return "{$prefix}/{$date}/{$sequence}";
    }
    
    /**
     * Calculate dan update total amount berdasarkan items
     */
    public function calculateTotal(): float
    {
        $total = $this->items()->sum('amount');
        $this->update(['total_biaya' => $total]);
        
        return $total;
    }
    
    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }
    
    /**
     * Get total after DP deduction (Grand Total - Total DP)
     */
    public function getGrandTotalAfterDpAttribute(): float
    {
        $totalDp = $this->items->sum(function($item) {
            return $item->tagihanOb->dp ?? 0;
        });
        
        return ($this->grand_total ?? 0) - $totalDp;
    }
    
    /**
     * Get formatted total after DP deduction
     */
    public function getFormattedTotalAfterDpAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total_after_dp, 0, ',', '.');
    }
    
    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'belum_realisasi' => 'bg-yellow-100 text-yellow-800',
            'sudah_realisasi' => 'bg-green-100 text-green-800',
            // Legacy status support
            'draft' => 'bg-yellow-100 text-yellow-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
    
    /**
     * Get formatted status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'belum_realisasi' => 'Belum Realisasi',
            'sudah_realisasi' => 'Sudah Realisasi',
            // Legacy status support
            'draft' => 'Belum Realisasi',
            'pending' => 'Belum Realisasi',
            'approved' => 'Sudah Realisasi',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status)
        };
    }
    
    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }
    
    /**
     * Scope untuk filter berdasardar range tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pranota', [$startDate, $endDate]);
    }
}
