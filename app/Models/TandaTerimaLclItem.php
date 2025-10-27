<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaTerimaLclItem extends Model
{
    use HasFactory;
    
    protected $table = 'tanda_terima_lcl_items';
    
    protected $fillable = [
        'tanda_terima_lcl_id',
        'item_number',
        'panjang',
        'lebar',
        'tinggi',
        'meter_kubik',
        'tonase'
    ];
    
    protected $casts = [
        'item_number' => 'integer',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'meter_kubik' => 'decimal:6',
        'tonase' => 'decimal:2'
    ];
    
    // Relationships
    public function tandaTerima(): BelongsTo
    {
        return $this->belongsTo(TandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }
    
    // Helper methods
    public function getFormattedVolumeAttribute(): string
    {
        return number_format($this->meter_kubik ?? 0, 6) . ' m³';
    }
    
    public function getFormattedWeightAttribute(): string
    {
        return number_format($this->tonase ?? 0, 2) . ' Ton';
    }
    
    public function getDimensionsAttribute(): string
    {
        return "{$this->panjang} x {$this->lebar} x {$this->tinggi} cm";
    }
    
    // Calculate volume from dimensions
    public function calculateVolume(): float
    {
        if ($this->panjang && $this->lebar && $this->tinggi) {
            // Convert cm³ to m³ (divide by 1,000,000)
            return ($this->panjang * $this->lebar * $this->tinggi) / 1000000;
        }
        return 0;
    }
    
    // Auto-calculate volume when dimensions are set
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            if ($item->panjang && $item->lebar && $item->tinggi) {
                $item->meter_kubik = $item->calculateVolume();
            }
        });
    }
}
