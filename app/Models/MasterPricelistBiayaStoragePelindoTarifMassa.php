<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPricelistBiayaStoragePelindoTarifMassa extends Model
{
    protected $table = 'master_pricelist_biaya_storage_pelindo_tarif_massas';

    protected $fillable = ['massa', 'tarif'];

    protected $casts = [
        'massa' => 'decimal:2',
        'tarif' => 'decimal:2',
    ];

    public function pricelist()
    {
        return $this->belongsTo(MasterPricelistBiayaStoragePelindo::class, 'pricelist_id');
    }
}
