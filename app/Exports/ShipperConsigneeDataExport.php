<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipperConsigneeDataExport extends StringValueBinder implements FromQuery, WithCustomValueBinder, WithHeadings, WithMapping, WithStyles
{
    private int $rowNumber = 0;

    public function __construct(private readonly Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
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

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->shipper,
            $row->alamat_shipper,
            $row->npwp_shipper,
            $row->consignee,
            $row->alamat_consignee,
            $row->npwp_consignee,
            $row->notify_party_consignee,
            $row->alamat_notify_party_consignee,
            $row->npwp_notify_party_consignee,
            $row->delivery_address_contact_person,
            $row->document_ppftz_03,
            $row->condition,
            $row->status === null ? '' : ($row->status ? 'Aktif' : 'Tidak Aktif'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->setAutoFilter('A1:N1');
        $sheet->freezePane('A2');

        return [];
    }
}
