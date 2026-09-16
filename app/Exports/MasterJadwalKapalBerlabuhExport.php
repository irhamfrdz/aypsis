<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MasterJadwalKapalBerlabuhExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $jadwals;
    protected $filterPelabuhan;

    public function __construct($jadwals, $filterPelabuhan = null)
    {
        $this->filterPelabuhan = $filterPelabuhan;
        $this->jadwals = collect($jadwals)->map(function ($item, $index) {
            return [
                $index + 1,
                $item->pelabuhan ?? '-',
                $item->nama_kapal ?? '-',
                $item->no_voyage ?? '-',
                $item->tanggal_closing ? \Carbon\Carbon::parse($item->tanggal_closing)->format('d-M-y') : '-',
                $item->tanggal_etd ? \Carbon\Carbon::parse($item->tanggal_etd)->format('d-M-y') : '-',
                $item->tanggal_eta ? \Carbon\Carbon::parse($item->tanggal_eta)->format('d-M-y') : '-',
                ucfirst($item->status ?? '-'),
                $item->keterangan ?? '-',
            ];
        });
    }

    public function collection()
    {
        return $this->jadwals;
    }

    public function headings(): array
    {
        return [
            'No',
            'Pelabuhan / Rute',
            'Nama Kapal',
            'Voyage',
            'Close',
            'ETD',
            'ETA',
            'Status',
            'Keterangan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Style Header Row
                $sheet->getStyle('A1:I1')->getFont()->setBold(true);
                $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Default header background for standard columns
                $sheet->getStyle('A1:D1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0F2FE'); // light sky blue

                // Close column header
                $sheet->getStyle('E1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0F2FE');

                // ETD column header (Yellow as in user's reference)
                $sheet->getStyle('F1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00'); // Yellow

                // ETA column header (Red with white text as in user's reference)
                $sheet->getStyle('G1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFF0000'); // Red
                $sheet->getStyle('G1')->getFont()->getColor()->setARGB('FFFFFFFF');

                // Status & Keterangan
                $sheet->getStyle('H1:I1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0F2FE');

                // Center align specific columns for all data rows
                if ($highestRow > 1) {
                    $sheet->getStyle("A2:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E2:G{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H2:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Borders
                    $sheet->getStyle("A1:I{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }
            },
        ];
    }
}
