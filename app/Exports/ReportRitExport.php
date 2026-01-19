<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class ReportRitExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $suratJalans;
    protected $startDate;
    protected $endDate;

    public function __construct($suratJalans, $startDate, $endDate)
    {
        $this->suratJalans = $suratJalans;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->suratJalans->map(function($sj, $index) {
            // helper to access both array and object
            $get = function($key, $default = null) use ($sj) {
                if (is_array($sj)) return $sj[$key] ?? $default;
                if (is_object($sj)) return $sj->$key ?? $default;
                return $default;
            };

            // Format tanggal — gunakan tanggal_checkpoint dulu, lalu tanggal_tanda_terima
            $tanggal = '-';
            $rawTanggal = $get('tanggal_checkpoint') ?: $get('tanggal_tanda_terima');
            if ($rawTanggal) {
                $tanggal = is_string($rawTanggal)
                    ? Carbon::parse($rawTanggal)->format('d/m/Y')
                    : (method_exists($rawTanggal, 'format') ? $rawTanggal->format('d/m/Y') : Carbon::parse($rawTanggal)->format('d/m/Y'));
            }

            // No Surat Jalan
            $noSuratJalan = $get('no_surat_jalan') ? $get('no_surat_jalan') : (
                ($get('order')) ? (is_array($get('order')) ? ($get('order')['nomor_order'] ?? '-') : ($get('order')->nomor_order ?? '-')) : '-'
            );

            // Kegiatan
            $kegiatan = ucfirst(strtolower($get('kegiatan') ? $get('kegiatan') : 'tarik isi'));

            // Supir
            $supir = $get('supir') ? $get('supir') : ($get('supir2') ? $get('supir2') : '-');

            // Pengirim, Penerima, Jenis Barang
            $pengirim = $get('pengirim') ? $get('pengirim') : '-';
            $penerima = $get('penerima') ? $get('penerima') : '-';
            $jenisBarang = $get('jenis_barang') ? $get('jenis_barang') : '-';

            // Tipe
            $tipe = $get('tipe_kontainer') ? $get('tipe_kontainer') : ($get('size') ? $get('size') : (
                ($get('order')) ? (is_array($get('order')) ? ($get('order')['tipe_kontainer'] ?? '-') : ($get('order')->tipe_kontainer ?? '-')) : '-'
            ));

            return [
                $index + 1,
                $tanggal,
                $noSuratJalan,
                $kegiatan,
                $supir,
                $get('no_plat') ? $get('no_plat') : '-',
                $pengirim,
                $penerima,
                $jenisBarang,
                $tipe,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No. Surat Jalan',
            'Kegiatan',
            'Supir',
            'No. Plat',
            'Pengirim',
            'Penerima',
            'Jenis Barang',
            'Tipe',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style header
                $event->sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Add borders to all cells with data
                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('A1:J' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Center align nomor column
                $event->sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add title
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->setCellValue('A1', 'REPORT RIT');
                $event->sheet->setCellValue('A2', 'Periode: ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y'));
                
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->mergeCells('A2:J2');
                
                $event->sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('A2:J2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Set row height for title rows
                $event->sheet->getRowDimension('1')->setRowHeight(25);
                $event->sheet->getRowDimension('2')->setRowHeight(20);
            },
        ];
    }
}
