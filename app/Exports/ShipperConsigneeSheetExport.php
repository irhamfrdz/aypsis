<?php

namespace App\Exports;

use App\Models\ShipperConsignee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipperConsigneeSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    public function collection()
    {
        return ShipperConsignee::all();
    }

    public function headings(): array
    {
        return [
            'TELEPHON',
            'HS CODE',
            'COMODITY',
            "ALAMAT \nEMAIL",
            'NITKU SHIPPER',
            'SHIPPER',
            'ADDRESS',
            "NO. IDENTITAS \n(NPWP  SHIPPER)",
            'CONSIGNEE',
            'ADDRESS',
            "NO. IDENTITAS \n(NPWP  CONSIGNEE)",
            "NOTIFY PARTY\n(CONSIGNEE)",
            'ADDRESS',
            "NO. IDENTITAS \n(NPWP  NOTIFY PARTY CONSIGNEE)",
            'DELIVERY ADDRESS & CONTACT PERSON',
            "DOCUMENT\n PPFTZ-03",
            'CONDITION',
            'IU BP KAWASAN',
            'NITKU CONSIGNEE',
            "NO. IDENTITAS \n(NPWP  CONSIGNEE)\n16 DIGIT",
        ];
    }

    public function map($row): array
    {
        return [
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
            $row->notify_party,
            $row->alamat_notify_party,
            $row->npwp_notify_party,
            $row->delivery_address,
            '', // DOCUMENT PPFTZ-03
            '', // CONDITION
            '', // IU BP KAWASAN
            $row->nitku_consignee,
            $row->npwp_consignee, // T: 16 DIGIT
        ];
    }

    public function title(): string
    {
        return 'Alamat Barang';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:T1')->getFont()->setBold(true);
        $sheet->getStyle('A1:T1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
            
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        $sheet->getStyle('A1:T' . ($sheet->getHighestRow()))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
