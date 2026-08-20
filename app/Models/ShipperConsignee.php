<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class ShipperConsignee extends Model
{
    use Auditable;

    protected $fillable = [
        'telepon',
        'hs_code',
        'commodity',
        'alamat_email',
        'nitku_shipper',
        'shipper',
        'alamat_shipper',
        'npwp_shipper',
        'consignee',
        'alamat_consignee',
        'npwp_consignee',
        'notify_party_consignee',
        'alamat_notify_party_consignee',
        'npwp_notify_party_consignee',
        'delivery_address',
        'nitku_consignee',
        'status',
    ];
}
