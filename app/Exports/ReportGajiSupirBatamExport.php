<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportGajiSupirBatamExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $gajiList;
    protected $startDate;
    protected $endDate;

    public function __construct($gajiList, $startDate, $endDate)
    {
        $this->gajiList = $gajiList;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('report-gaji-supir-batam.excel', [
            'gajiList' => $this->gajiList,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->gajiList) + 5; // Title (3) + Header (1) + Data + Total (1)

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $sheet->getStyle('A4:H' . $lastRow)->applyFromArray($styleArray);
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastRow . ':H' . $lastRow)->getFont()->setBold(true);

        return [];
    }
}
