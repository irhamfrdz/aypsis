<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class MobilTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'AT1122500001', 'B5598BBA', '1234', 'NAMA KARYAWAN', 'JKT', 'HONDA', 'SEPEDA MOTOR',
                '2020', 'R12345678', 'JBK1E1714025', 'MH1JBK116LK717264', '24 Sep 26', '24 Sep 30',
                '', '', 'FERRY KURNIAWAN', 'OWEN', 'ZURICH ASURANSI INDONESIA, PT', '26 Jun 26',
                'HITAM', 'MTR-JKT.031'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Aktiva', 'NO.POLISI', 'nik', 'nama_lengkap', 'LOKASI', 'MEREK', 'JENIS', 
            'TAHUN PEMBUATAN', 'BPKB', 'NO. MESIN', 'NO. RANGKA', 'PAJAK STNK', 'PAJAK PLAT', 
            'NO. KIR', 'PAJAK KIR', 'ATAS NAMA', 'PEMAKAI', 'ASURANSI', 'JTE ASURANSI', 
            'WARNA PLAT', 'Catatan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Make header row bold
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, 'B' => 12, 'C' => 10, 'D' => 20, 'E' => 10, 'F' => 15, 'G' => 15,
            'H' => 12, 'I' => 12, 'J' => 15, 'K' => 20, 'L' => 12, 'M' => 12, 'N' => 12,
            'O' => 12, 'P' => 20, 'Q' => 15, 'R' => 25, 'S' => 12, 'T' => 12, 'U' => 15
        ];
    }
}