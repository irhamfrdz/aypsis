<?php

namespace App\Exports;

use App\Models\ShipperConsignee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShipperContactExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        // Mengambil semua data shipper, lalu di-filter agar unique berdasarkan nama shipper
        $shippers = ShipperConsignee::whereNotNull('shipper')
            ->where('shipper', '!=', '')
            ->get(['shipper', 'contact_person']);
            
        return $shippers->unique('shipper')->map(function($item) {
            return [
                'Shipper' => $item->shipper,
                'Contact Person' => $item->contact_person,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Shipper',
            'Contact Person',
        ];
    }
}
