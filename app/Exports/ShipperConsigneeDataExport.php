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

class ShipperConsigneeDataExport extends StringValueBinder implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomValueBinder
{
    public function __construct(private readonly Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
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
            'Contact Person',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->telepon,
            $row->hs_code,
            $row->commodity,
            $row->alamat_email,
            $row->nitku_shipper,
            $row->shipper,
            $row->alamat_shipper,
            $row->npwp_shipper,
            $row->consignee,
            $row->alamat_consignee,
            $row->npwp_consignee,
            $row->notify_party_consignee,
            $row->alamat_notify_party_consignee,
            $row->npwp_notify_party_consignee,
            $row->delivery_address,
            $row->nitku_consignee,
            $row->document_ppftz_03,
            $row->condition,
            $row->ip_bp_kawasan,
            $row->npwp_consignee_16_digit,
            $row->contact_person,
            $row->status === null ? '' : ($row->status ? 'Aktif' : 'Tidak Aktif'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:W1')->getFont()->setBold(true);
        $sheet->setAutoFilter('A1:W1');
        $sheet->freezePane('A2');

        return [];
    }
}
