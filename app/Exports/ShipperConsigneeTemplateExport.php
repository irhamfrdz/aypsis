<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipperConsigneeTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function array(): array
    {
        return [
            [
                '1',
                'PT. SHIPPER MAJU JAYA',
                'JL. JEND SUDIRMAN JAKARTA',
                '01.234.567.8-000.000',
                'PT. CONSIGNEE SEJAHTERA',
                'JL. GATOT SUBROTO JAKARTA',
                '02.345.678.9-000.000',
                'PT. NOTIFY PARTY (CONSIGNEE)',
                'JL. MH THAMRIN JAKARTA',
                '03.456.789.0-000.000',
                'JL. DELIVERY NO. 1 Telp. 08123456789 / Bpk. Budi',
                'NON AYP',
                'PTD (I B)',
                'Aktif',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'No.',
            'SHIPPER',
            'ADDRESS',
            'NO. IDENTITAS (NPWP SHIPPER)',
            'CONSIGNEE',
            'ADDRESS',
            'NO. IDENTITAS (NPWP CONSIGNEE)',
            'NOTIFY PARTY (CONSIGNEE)',
            'ADDRESS',
            'NO. IDENTITAS (NPWP NOTIFY PARTY CONSIGNEE)',
            'DELIVERY ADDRESS & CONTACT PERSON',
            'DOCUMENT PPFTZ-03',
            'CONDITION',
            'Status',
        ];
    }
}
