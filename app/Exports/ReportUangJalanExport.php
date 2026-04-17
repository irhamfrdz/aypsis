<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportUangJalanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $uangJalans;
    protected $startDate;
    protected $endDate;

    public function __construct($uangJalans, $startDate, $endDate)
    {
        $this->uangJalans = $uangJalans;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->uangJalans;
    }

    public function headings(): array
    {
        return [
            ['REPORT RINCIAN UANG JALAN'],
            ['Periode: ' . $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y')],
            [''],
            [
                'No',
                'Tanggal',
                'Nomor UJ',
                'No. Bukti (Accurate)',
                'No. Surat Jalan',
                'Tipe',
                'Tujuan Ambil',
                'Supir',
                'NIK',
                'Plat Nomor',
                'Uang Jalan (Nominal)',
                'Mel',
                'Pelancar',
                'Kawalan',
                'Parkir',
                'Total Lain-lain',
                'GRAND TOTAL',
                'Dibuat Oleh'
            ]
        ];
    }

    public function map($uj): array
    {
        static $index = 0;
        $index++;

        $relatedSJ = $uj->suratJalan ?? $uj->suratJalanBongkaran;
        $typeLabel = $uj->surat_jalan_id ? 'Muat' : ($uj->surat_jalan_bongkaran_id ? 'Bongkar' : '-');
        $sjNumber = $uj->suratJalan ? $uj->suratJalan->no_surat_jalan : ($uj->suratJalanBongkaran ? $uj->suratJalanBongkaran->nomor_surat_jalan : '-');
        $supir = $relatedSJ->supir ?? '-';
        $plat = $relatedSJ->no_plat ?? '-';
        $nik = $relatedSJ->supirKaryawan->nik ?? '-';
        $tujuanAmbil = $relatedSJ->tujuan_pengambilan ?? '-';
        
        $pembayaran = $uj->pranotaUangJalan->flatMap->pembayaranPranotaUangJalans->sortByDesc('tanggal_pembayaran')->first();
        $noBukti = $pembayaran ? $pembayaran->nomor_accurate : '-';

        $lainLain = ($uj->jumlah_mel ?? 0) + ($uj->jumlah_pelancar ?? 0) + ($uj->jumlah_kawalan ?? 0) + ($uj->jumlah_parkir ?? 0);
        return [
            $index,
            $uj->tanggal_uang_jalan->format('d/m/Y'),
            $uj->nomor_uang_jalan,
            $noBukti,
            $sjNumber,
            $typeLabel,
            $tujuanAmbil,
            $supir,
            $nik,
            $plat,
            (float)($uj->jumlah_uang_jalan ?? 0),
            (float)($uj->jumlah_mel ?? 0),
            (float)($uj->jumlah_pelancar ?? 0),
            (float)($uj->jumlah_kawalan ?? 0),
            (float)($uj->jumlah_parkir ?? 0),
            (float)$lainLain,
            (float)($uj->jumlah_total ?? 0),
            $uj->createdBy->name ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:R1');
        $sheet->mergeCells('A2:R2');
        
        // Final Row
        $lastRow = $sheet->getHighestRow();
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B45309'] // Amber 700
                ]
            ],
            'A1:R' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}
