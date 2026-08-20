<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShipperConsigneeTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                '08123456789',
                '1234.56.78',
                'ELECTRONICS',
                'shipper@example.com',
                'NITKU-123',
                'PT. SHIPPER MAJU JAYA',
                'JL. JEND SUDIRMAN JAKARTA',
                '01.234.567.8-000.000',
                'PT. CONSIGNEE SEJAHTERA',
                'JL. GATOT SUBROTO JAKARTA',
                '02.345.678.9-000.000',
                'PT. NOTIFY PARTY (CONSIGNEE)',
                'JL. MH THAMRIN JAKARTA',
                '03.456.789.0-000.000',
                'JL. DELIVERY NO. 1',
                'NITKU-456',
                'DOC-1234',
                'GOOD',
                'IP-5678',
                '1234567890123456',
                'Aktif'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Telepon',
            'HS Code',
            'Commodity',
            'Alamat Email',
            'NITKU Shipper',
            'Shipper',
            'Alamat Shipper',
            'NPWP Shipper',
            'Consignee',
            'Alamat Consignee',
            'NPWP Consignee',
            'Notify Party (Consignee)',
            'Alamat Notify Party (Consignee)',
            'NPWP Notify Party (Consignee)',
            'Delivery Address',
            'NITKU Consignee',
            'Document PPFTZ-03',
            'Condition',
            'IP BP Kawasan',
            'NPWP Consignee (16 Digit)',
            'Status'
        ];
    }
}
