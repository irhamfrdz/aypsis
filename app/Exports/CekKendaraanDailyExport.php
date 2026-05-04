<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CekKendaraanDailyExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $drivers;
    protected $checksForDate;
    protected $date;

    public function __construct($drivers, $checksForDate, $date)
    {
        $this->drivers = $drivers;
        $this->checksForDate = $checksForDate;
        $this->date = $date;
    }

    public function collection()
    {
        return $this->drivers;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN CEK KENDARAAN HARIAN'],
            ['Tanggal: ' . \Carbon\Carbon::parse($this->date)->format('d F Y')],
            [''],
            [
                'No',
                'Nama Supir',
                'NIK',
                'Pekerjaan',
                'Nomor Polisi',
                'Kendaraan',
                'Odometer',
                'Jam Cek',
                'Status'
            ]
        ];
    }

    public function map($driver): array
    {
        static $no = 0;
        $no++;

        $driverChecks = $this->checksForDate->get($driver->id);
        $check = $driverChecks ? $driverChecks->first() : null;
        
        return [
            $no,
            $driver->nama_lengkap,
            $driver->nik ?? '-',
            $driver->pekerjaan ?? '-',
            $check ? ($check->mobil->nomor_polisi ?? '-') : '-',
            $check ? (($check->mobil->merk ?? '') . ' ' . ($check->mobil->tipe ?? '')) : '-',
            $check && $check->odometer ? number_format($check->odometer, 0, ',', '.') : '-',
            $check ? \Carbon\Carbon::parse($check->jam)->format('H:i') . ' WIB' : '-',
            $check ? 'SELESAI' : 'BELUM CEK'
        ];
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
