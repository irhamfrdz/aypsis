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
        'status_kontainer',
        'status',
        'keterangan',
    ];

    public function tarifMassa()
    {
        return $this->hasMany(MasterPricelistBiayaStoragePelindoTarifMassa::class, 'pricelist_id')->orderBy('massa');
    }
}
