<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalTandaTerimaDuaShipperOverride extends Model
{
    protected $table = 'approval_tanda_terima_2_shipper_overrides';

    protected $fillable = [
        'source_type',
        'source_id',
        'context',
        'shipper_jb_id',
        'fields',
        'updated_by',
    ];

    protected $casts = [
        'fields' => 'array',
    ];
}
