<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPricelistLabuhTambat extends Model
{
    protected $table = 'master_pricelist_labuh_tambat';

    protected $fillable = [
        'nama_tarif',
        'biaya',
        'satuan',
        'keterangan',
        'is_active',
    ];
}
