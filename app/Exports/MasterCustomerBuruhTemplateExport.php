<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterCustomerBuruhTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            // Dummy data row for example
            ['Contoh Customer', 'BCA', '1234567890', 'Bapak Contoh'],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_customer',
            'bank',
            'nomor_rekening',
            'penerima',
        ];
    }
}
