<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPricelistOb extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_pricelist_ob';

    protected $fillable = [
        'size_kontainer',
        'status_kontainer',
        'status_service',
        'biaya',
        'keterangan',
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
            'empty' => 'Empty',
        ];
    }

    public static function getStatusServiceOptions()
    {
        return [
            'service' => 'Service',
            'non_service' => 'Bukan Service',
        ];
    }

    /**
     * Get the size kontainer options
     */
    public static function getSizeKontainerOptions()
    {
        return [
            '20ft' => '20 ft',
            '40ft' => '40 ft',
        ];
    }

    /**
     * Accessor for formatted biaya
     */
    public function getFormattedBiayaAttribute()
    {
        return 'Rp '.number_format($this->biaya, 0, ',', '.');
    }

    /**
     * Accessor for status kontainer label
     */
    public function getStatusKontainerLabelAttribute()
    {
        $options = self::getStatusKontainerOptions();

        return $options[$this->status_kontainer] ?? $this->status_kontainer;
    }

    public function getStatusServiceLabelAttribute()
    {
        $options = self::getStatusServiceOptions();

        return $options[$this->status_service] ?? $this->status_service;
    }

    /**
     * Accessor for size kontainer label
     */
    public function getSizeKontainerLabelAttribute()
    {
        $options = self::getSizeKontainerOptions();

        return $options[$this->size_kontainer] ?? $this->size_kontainer;
    }
}
