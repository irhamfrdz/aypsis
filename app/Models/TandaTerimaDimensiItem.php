<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTerimaDimensiItem extends Model
{
    use HasFactory;

    protected $table = 'tanda_terima_dimensi_items';

    protected $fillable = [
        'tanda_terima_tanpa_surat_jalan_id',
        'nama_barang',
        'jumlah',
        'satuan',
        'panjang',
        'lebar',
        'tinggi',
        'meter_kubik',
        'tonase',
        'item_order'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'meter_kubik' => 'decimal:6',
        'tonase' => 'decimal:2',
        'item_order' => 'integer'
    ];

    /**
     * Get the parent tanda terima
     */
    public function tandaTerima()
    {
        return $this->belongsTo(TandaTerimaTanpaSuratJalan::class, 'tanda_terima_tanpa_surat_jalan_id');
    }

    /**
     * Calculate volume automatically
     * NOTE: This returns volume in m³ (not mm³)
     */
    public function calculateVolume()
    {
        if ($this->panjang && $this->lebar && $this->tinggi) {
            // Values are already in meters, so result is in m³
            return $this->panjang * $this->lebar * $this->tinggi;
        }
        return 0;
    }

    /**
     * Boot method to auto-calculate volume
     * DISABLED: Form already sends calculated volume in m³, no need to recalculate
     * The division by 1000000 was causing incorrect values (0.000002 instead of 1.944)
     */
    protected static function boot()
    {
        parent::boot();

        // DISABLED: This was dividing by 1000000 incorrectly
        // The form already sends meter_kubik in m³, not mm³
        /*
        static::saving(function ($model) {
            if ($model->panjang && $model->lebar && $model->tinggi) {
                $model->meter_kubik = ($model->panjang * $model->lebar * $model->tinggi) / 1000000;
            }
        });
        */
    }
}
