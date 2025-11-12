<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistTarifHistory extends Model
{
    protected $table = 'pricelist_tarif_history';

    protected $fillable = [
        'pricelist_uang_jalan_batam_id',
        'kelola_bbm_id',
        'tarif_lama',
        'tarif_baru',
        'persentase_perubahan',
        'persentase_bbm',
        'keterangan',
    ];

    protected $casts = [
        'tarif_lama' => 'decimal:2',
        'tarif_baru' => 'decimal:2',
        'persentase_perubahan' => 'decimal:2',
        'persentase_bbm' => 'decimal:2',
    ];

    /**
     * Relasi ke pricelist uang jalan Batam
     */
    public function pricelist()
    {
        return $this->belongsTo(PricelistUangJalanBatam::class, 'pricelist_uang_jalan_batam_id');
    }

    /**
     * Relasi ke kelola BBM
     */
    public function kelolaBbm()
    {
        return $this->belongsTo(KelolaBbm::class, 'kelola_bbm_id');
    }
}
