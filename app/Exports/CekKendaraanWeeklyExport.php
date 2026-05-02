<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class CekKendaraanWeeklyExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $drivers;
    protected $checksForWeek;
    protected $weekStart;

    public function __construct($drivers, $checksForWeek, $weekStart)
    {
        $this->drivers = $drivers;
        $this->checksForWeek = $checksForWeek;
        $this->weekStart = $weekStart;
    }

    public function collection()
    {
        return $this->drivers;
    }

    public function headings(): array
    {
        $weekEnd = $this->weekStart->copy()->addDays(6);
        return [
            ['LAPORAN CEK KENDARAAN MINGGUAN'],
            ['Periode: ' . $this->weekStart->format('d F Y') . ' - ' . $weekEnd->format('d F Y')],
            [''],
            [
                'No',
                'Nama Supir',
                'NIK',
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu',
                'Minggu',
                'Total Cek'
            ]
        ];
    }

    public function map($driver): array
    {
        static $no = 0;
        $no++;

        $row = [
            $no,
            $driver->nama_lengkap,
            $driver->nik ?? '-'
        ];

        $totalCek = 0;
        $driverChecks = $this->checksForWeek->get($driver->id);

        for ($i = 0; $i < 7; $i++) {
            $dateKey = $this->weekStart->copy()->addDays($i)->format('Y-m-d');
            $hasCheck = $driverChecks && $driverChecks->has($dateKey);
            
            if ($hasCheck) {
                $row[] = 'V';
                $totalCek++;
            } else {
                $row[] = '-';
            }
        }

        $row[] = $totalCek . '/7';

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]],
        ];
    }
}
