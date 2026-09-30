<?php

namespace App\Imports;

use App\Models\ShipperConsignee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ShipperConsigneeImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        $shipper = $row[1] ?? $row['shipper'] ?? null;
        $consignee = $row[4] ?? $row['consignee'] ?? null;

        // Skip completely empty rows
        if (empty($shipper) && empty($consignee)) {
            return null;
        }

        $statusInput = $row[13] ?? $row['status'] ?? 1;
        $status = 1; // Default Aktif
        if (is_string($statusInput) && in_array(strtolower(trim($statusInput)), ['tidak aktif', 'non aktif', 'non-aktif'])) {
            $status = 0;
        } elseif (is_numeric($statusInput) && $statusInput == 0) {
            $status = 0;
        }

        return new ShipperConsignee([
            'shipper' => $shipper,
            'alamat_shipper' => $row[2] ?? $row['alamat_shipper'] ?? null,
            'npwp_shipper' => $row[3] ?? $row['npwp_shipper'] ?? null,
            'consignee' => $consignee,
            'alamat_consignee' => $row[5] ?? $row['alamat_consignee'] ?? null,
            'npwp_consignee' => $row[6] ?? $row['npwp_consignee'] ?? null,
            'notify_party_consignee' => $row[7] ?? $row['notify_party_consignee'] ?? ($row['notify_party'] ?? null),
            'alamat_notify_party_consignee' => $row[8] ?? $row['alamat_notify_party_consignee'] ?? ($row['alamat_notify_party'] ?? null),
            'npwp_notify_party_consignee' => $row[9] ?? $row['npwp_notify_party_consignee'] ?? ($row['npwp_notify_party'] ?? null),
            'delivery_address_contact_person' => $row[10] ?? $row['delivery_address_contact_person'] ?? ($row['delivery_address'] ?? null),
            'document_ppftz_03' => $row[11] ?? $row['document_ppftz_03'] ?? null,
            'condition' => $row[12] ?? $row['condition'] ?? null,
            'status' => $status,
        ]);
    }
}
