<?php

namespace App\Exports;

use App\Models\PayrollUangMakan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PayrollUangMakanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $penempatan;

    public function __construct($startDate, $endDate, $penempatan = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->penempatan = $penempatan;
    }

    public function collection()
    {
        $query = PayrollUangMakan::with('karyawan')
            ->where('periode_start', $this->startDate)
            ->where('periode_end', $this->endDate);

        if (!empty($this->penempatan)) {
            $query->whereHas('karyawan', function($q) {
                $q->where('penempatan', $this->penempatan);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        $startDateFormatted = \Carbon\Carbon::parse($this->startDate)->format('d F Y');
        $endDateFormatted = \Carbon\Carbon::parse($this->endDate)->format('d F Y');
        $penempatanStr = empty($this->penempatan) ? 'Semua Lokasi' : $this->penempatan;

        return [
            ['LAPORAN PAYROLL UANG MAKAN'],
            ['Periode: ' . $startDateFormatted . ' s/d ' . $endDateFormatted],
            ['Penempatan: ' . $penempatanStr],
            [],
            [
                'NIK',
                'Nama Karyawan',
                'Penempatan',
                'Total Kehadiran',
                'Multiplier',
                'Nominal Uang Makan',
                'Total Payout'
            ]
        ];
    }

    public function map($row): array
    {
        return [
            $row->karyawan->nik ?? '-',
            $row->karyawan->nama_lengkap ?? '-',
            $row->karyawan->penempatan ?? '-',
            $row->total_kehadiran,
            $row->multiplier . 'x',
            $row->nominal_per_hari,
            $row->total_payout
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER,
            'F' => '"Rp"#,##0',
            'G' => '"Rp"#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Merge Title cells
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');
                
                // Style Title
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ]
                ]);
                
                $sheet->getStyle('A1')->getFont()->setSize(14);

                // Style Table Headings (Row 5)
                $sheet->getStyle('A5:G5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF0E7490'] // Cyan-700
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // Auto Add Borders to Table
                if ($highestRow >= 5) {
                    $sheet->getStyle('A5:G' . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
                }
                
                // Center align specific columns
                $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D6:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
