<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPricelistTujuanKontainerSewa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_pricelist_tujuan_kontainer_sewas';

    protected $fillable = [
        'tujuan',
        'ongkos_truk_20ft',
        'ongkos_truk_40ft',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'ongkos_truk_20ft' => 'decimal:2',
        'ongkos_truk_40ft' => 'decimal:2',
    ];
}
