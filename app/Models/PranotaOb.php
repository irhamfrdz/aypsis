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
        'total_amount',
        'status',
        'periode',
        'created_by',
        'approved_at',
        'approved_by'
    ];
    
    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_amount' => 'decimal:2',
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
        $this->update(['total_amount' => $total]);
        
        return $total;
    }
    
    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
    
    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
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
