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
        $groupCount = $this->data['groups']->count();
        $detailHeader = 6 + $groupCount;
        $lastRow = $detailHeader + $groupCount + $this->data['rows']->count() + 1;
        $sheet->getStyle('A1:P1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        $sheet->getStyle('A'.$detailHeader.':P'.$detailHeader)->getFont()->setBold(true);
        $sheet->getStyle('A'.$lastRow.':P'.$lastRow)->getFont()->setBold(true);
        $sheet->getStyle('D6:G'.(5 + $groupCount))->getNumberFormat()->setFormatCode('#,##0;[Red](#,##0)');
        $sheet->getStyle('J'.($detailHeader + 1).':J'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('K'.($detailHeader + 1).':P'.$lastRow)->getNumberFormat()->setFormatCode('#,##0;[Red](#,##0)');
        $sheet->freezePane('A'.($detailHeader + 1));

        return [];
    }
}
