<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PranotaObItem extends Model
{
    protected $table = 'pranota_ob_items';
    
    protected $fillable = [
        'pranota_ob_id',
        'tagihan_ob_id',
        'amount',
        'notes'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2'
    ];
    
    /**
     * Relasi ke pranota OB
     */
    public function pranotaOb(): BelongsTo
    {
        return $this->belongsTo(PranotaOb::class);
    }
    
    /**
     * Relasi ke tagihan OB
     */
    public function tagihanOb(): BelongsTo
    {
        return $this->belongsTo(TagihanOb::class);
    }
    
    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
    
    /**
     * Boot method untuk auto-update total pranota saat item berubah
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saved(function ($item) {
            $item->pranotaOb->calculateTotal();
        });
        
        static::deleted(function ($item) {
            $item->pranotaOb->calculateTotal();
        });
    }
}
