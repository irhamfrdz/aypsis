<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class MasterPricelistFreight extends Model
{
    use HasFactory, Auditable;

    protected $table = 'master_pricelist_freights';

    protected $fillable = [
        'pelabuhan_asal_id',
        'pelabuhan_tujuan_id',
        'size_kontainer',
        'biaya',
        'keterangan'
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    /**
     * Get the size kontainer options
     */
    public static function getSizeKontainerOptions()
    {
        return [
            '20ft' => '20 ft',
            '40ft' => '40 ft',
            '20ft reefer' => '20 ft Reefer',
            '40ft reefer' => '40 ft Reefer',
            'flatrack' => 'Flatrack',
            'opentop' => 'Opentop',
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
     * Accessor for size kontainer label
     */
    public function getSizeKontainerLabelAttribute()
    {
        $options = self::getSizeKontainerOptions();
        return $options[$this->size_kontainer] ?? $this->size_kontainer;
    }

    /**
     * Relationship to origin port
     */
    public function asal()
    {
        return $this->belongsTo(MasterPelabuhan::class, 'pelabuhan_asal_id');
    }

    /**
     * Relationship to destination port
     */
    public function tujuan()
    {
        return $this->belongsTo(MasterPelabuhan::class, 'pelabuhan_tujuan_id');
    }
}
