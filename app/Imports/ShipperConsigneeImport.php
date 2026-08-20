<?php

namespace App\Imports;

use App\Models\ShipperConsignee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ShipperConsigneeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $statusInput = $row['status'] ?? 1;
        $status = 1; // Default Aktif
        if (is_string($statusInput) && in_array(strtolower(trim($statusInput)), ['tidak aktif', 'non aktif', 'non-aktif'])) {
            $status = 0;
        } elseif (is_numeric($statusInput) && $statusInput == 0) {
            $status = 0;
        }

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
            'notify_party_consignee' => $row['notify_party_consignee'] ?? ($row['notify_party'] ?? null),
            'alamat_notify_party_consignee' => $row['alamat_notify_party_consignee'] ?? ($row['alamat_notify_party'] ?? null),
            'npwp_notify_party_consignee' => $row['npwp_notify_party_consignee'] ?? ($row['npwp_notify_party'] ?? null),
            'delivery_address' => $row['delivery_address'] ?? null,
            'nitku_consignee' => $row['nitku_consignee'] ?? null,
            'status' => $status,
        ]);
    }
}
