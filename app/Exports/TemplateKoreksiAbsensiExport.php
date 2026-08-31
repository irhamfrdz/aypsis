<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateKoreksiAbsensiExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $headersList;

    public function __construct($headersList)
    {
        $this->headersList = $headersList;
    }

    public function headings(): array
    {
        return $this->headersList;
    }

    public function array(): array
    {
        return [
            ['1234', 'Contoh Nama', '2026-08-31', '08:00', '17:00']
        ];
    }
}
