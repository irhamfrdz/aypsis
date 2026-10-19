<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPricelistBiayaStoragePelindo extends Model
{
    protected $table = 'master_pricelist_biaya_storage_pelindos';

    protected $fillable = [
        'vendor',
        'lokasi',
        'size_kontainer',
        'tarif_massa_1',
        'tarif_massa_2',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tarif_massa_1' => 'decimal:2',
        'tarif_massa_2' => 'decimal:2',
    ];
}
