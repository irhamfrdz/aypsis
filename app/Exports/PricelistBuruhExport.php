<?php

namespace App\Exports;

use App\Models\PricelistBuruh;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PricelistBuruhExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PricelistBuruh::orderBy('barang')->orderBy('size')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Barang',
            'Size',
            'Tipe',
            'Tarif',
            'Status',
            'Keterangan',
        ];
    }

    /**
     * @var PricelistBuruh $item
     */
    public function map($item): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $item->barang,
            $item->size ?? '-',
            $item->tipe ?? '-',
            $item->tarif,
            $item->is_active ? 'Aktif' : 'Tidak Aktif',
            $item->keterangan ?? '-',
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
