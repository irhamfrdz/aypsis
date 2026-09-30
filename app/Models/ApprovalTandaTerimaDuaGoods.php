<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalTandaTerimaDuaGoods extends Model
{
    protected $table = 'approval_tanda_terima_2_goods';

    protected $fillable = [
        'source_type',
        'source_id',
        'goods',
        'keterangan_barang',
        'updated_by',
    ];

    protected $casts = [
        'goods' => 'array',
    ];
}
