<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalTandaTerimaDuaFclShipper extends Model
{
    protected $table = 'approval_tanda_terima_2_fcl_shippers';

    protected $fillable = ['tanda_terima_id', 'shipper_jb_id', 'manifest_goods', 'goods', 'updated_by'];

    protected $casts = ['manifest_goods' => 'array', 'goods' => 'array'];

    public function shipperJb()
    {
        return $this->belongsTo(ShipperConsignee::class, 'shipper_jb_id');
    }
}
