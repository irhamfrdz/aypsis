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
        'nama_barang',
        'lokasi',
        'vendor',
        'tarif',
        'status',
        'keterangan',
        'pelabuhan_asal_id',
        'pelabuhan_tujuan_id',
        'size_kontainer'
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
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
     * Accessor for formatted tarif
     */
    public function getFormattedTarifAttribute()
    {
        return 'Rp ' . number_format($this->tarif, 0, ',', '.');
    }

    /**
     * Compatibility accessor for old code using 'biaya'
     */
    public function getBiayaAttribute()
    {
        return $this->tarif;
    }

    public function getFormattedBiayaAttribute()
    {
        return $this->formatted_tarif;
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
