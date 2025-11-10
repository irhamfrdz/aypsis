<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class MasterPricelistOb extends Model
{
    use HasFactory, Auditable;

    protected $table = 'master_pricelist_ob';

    protected $fillable = [
        'size_kontainer',
        'status_kontainer', 
        'biaya',
        'keterangan'
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    /**
     * Get the status kontainer options
     */
    public static function getStatusKontainerOptions()
    {
        return [
            'full' => 'Full',
            'empty' => 'Empty'
        ];
    }

    /**
     * Get the size kontainer options
     */
    public static function getSizeKontainerOptions()
    {
        return [
            '20ft' => '20 ft',
            '40ft' => '40 ft'
        ];
    }

    /**
     * Accessor for formatted biaya
     */
    public function getFormattedBiayaAttribute()
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    /**
     * Accessor for status label
     */
    public function getStatusLabelAttribute()
    {
        $options = self::getStatusKontainerOptions();
        return $options[$this->status_kontainer] ?? $this->status_kontainer;
    }

    /**
     * Accessor for size label
     */
    public function getSizeLabelAttribute()
    {
        $options = self::getSizeKontainerOptions();
        return $options[$this->size_kontainer] ?? $this->size_kontainer;
    }
}
