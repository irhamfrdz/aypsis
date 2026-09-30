<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class ShipperConsignee extends Model
{
    use Auditable;

    protected $fillable = [
        'shipper',
        'alamat_shipper',
        'npwp_shipper',
        'consignee',
        'alamat_consignee',
        'npwp_consignee',
        'notify_party_consignee',
        'alamat_notify_party_consignee',
        'npwp_notify_party_consignee',
        'delivery_address_contact_person',
        'document_ppftz_03',
        'condition',
        'status',
    ];

    /**
     * Backwards compatibility for delivery_address.
     */
    public function getDeliveryAddressAttribute(): ?string
    {
        return $this->attributes['delivery_address_contact_person'] ?? null;
    }

    public function setDeliveryAddressAttribute($value): void
    {
        $this->attributes['delivery_address_contact_person'] = $value;
    }
}
