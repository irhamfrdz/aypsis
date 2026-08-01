<?php

namespace App\Imports;

use App\Models\ShipperConsignee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ShipperConsigneeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new ShipperConsignee([
            'telepon' => $row['telepon'] ?? null,
            'hs_code' => $row['hs_code'] ?? null,
            'commodity' => $row['commodity'] ?? null,
            'alamat_email' => $row['alamat_email'] ?? null,
            'nitku_shipper' => $row['nitku_shipper'] ?? null,
            'shipper' => $row['shipper'] ?? null,
            'alamat_shipper' => $row['alamat_shipper'] ?? null,
            'npwp_shipper' => $row['npwp_shipper'] ?? null,
            'consignee' => $row['consignee'] ?? null,
            'alamat_consignee' => $row['alamat_consignee'] ?? null,
            'npwp_consignee' => $row['npwp_consignee'] ?? null,
            'notify_party' => $row['notify_party'] ?? null,
            'alamat_notify_party' => $row['alamat_notify_party'] ?? null,
            'npwp_notify_party' => $row['npwp_notify_party'] ?? null,
            'delivery_address' => $row['delivery_address'] ?? null,
            'nitku_consignee' => $row['nitku_consignee'] ?? null,
            'status' => $row['status'] ?? 'Aktif',
        ]);
    }
}
