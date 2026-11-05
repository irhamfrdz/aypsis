<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BiayaAgenReportExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $data)
    {
    }

    public function view(): View
    {
        return view('report.biaya-agen.excel', $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data['rows']->count() + 6;
        $sheet->getStyle('A1:P1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:P5')->getFont()->setBold(true);
        $sheet->getStyle('A'.$lastRow.':P'.$lastRow)->getFont()->setBold(true);
        $sheet->getStyle('J6:J'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('K6:P'.$lastRow)->getNumberFormat()->setFormatCode('#,##0;[Red](#,##0)');
        $sheet->freezePane('A6');

        return [];
    }
}
