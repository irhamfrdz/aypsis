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
        'nama_barang',
        'jumlah',
        'satuan',
        'panjang',
        'lebar',
        'tinggi',
        'meter_kubik',
        'tonase'
    ];
    
    protected $casts = [
        'item_number' => 'integer',
        'jumlah' => 'integer',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'meter_kubik' => 'decimal:3', // 3 digit di belakang koma
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
        return number_format($this->meter_kubik ?? 0, 3) . ' m³';
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
    
    // Mutator: Round volume to 3 decimal places
    public function setMeterKubikAttribute($value)
    {
        if ($value !== null && is_numeric($value)) {
            // Selalu bulatkan ke 3 digit desimal
            $this->attributes['meter_kubik'] = round(floatval($value), 3);
        } else {
            $this->attributes['meter_kubik'] = $value;
        }
    }
    
    // Auto-calculate volume when dimensions are set
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            if ($item->panjang && $item->lebar && $item->tinggi) {
                $volume = floatval($item->panjang) * floatval($item->lebar) * floatval($item->tinggi);
                $item->meter_kubik = $volume; // Akan otomatis dibulatkan via mutator
            }
        });
    }
}
