<?php

namespace App\Exports;

use App\Models\InvoiceAktivitasLain;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportUangJalanExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $uangJalans;

    protected $startDate;

    protected $endDate;

    protected $adjustmentsByUjId;

    protected $adjustmentRowIndices = [];

    public function __construct($uangJalans, $startDate, $endDate, $adjustmentsByUjId = null)
    {
        $this->uangJalans = $uangJalans;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->adjustmentsByUjId = $adjustmentsByUjId ?? collect();
    }

    public function array(): array
    {
        $rows = [];
        $index = 0;
        $dataRowStart = 5; // headings take rows 1-4, data starts at row 5

        foreach ($this->uangJalans as $uj) {
            $index++;

            $standalonePayment = $uj->_standalone_payment ?? null;
            $relatedSJ = $uj->suratJalan ?? $uj->suratJalanBongkaran;
            $typeLabel = $standalonePayment ? 'Aktivitas Lain' : ($uj->surat_jalan_id ? 'Muat' : ($uj->surat_jalan_bongkaran_id ? 'Bongkar' : '-'));
            $sjNumber = $uj->suratJalan ? $uj->suratJalan->no_surat_jalan : ($uj->suratJalanBongkaran ? $uj->suratJalanBongkaran->nomor_surat_jalan : '-');
            $supir = $relatedSJ->supir ?? '-';
            $plat = $relatedSJ->no_plat ?? '-';
            $nik = $relatedSJ->supirKaryawan->nik ?? '-';
            $tujuanAmbil = $relatedSJ->tujuan_pengambilan ?? '-';
            $namaBarang = $relatedSJ->jenis_barang ?? '-';

            $pembayaran = $uj->pranotaUangJalan->flatMap->pembayaranPranotaUangJalans->sortByDesc('tanggal_pembayaran')->first();
            $noBukti = $standalonePayment ? ($standalonePayment->nomor_accurate ?: '-') : ($pembayaran ? $pembayaran->nomor_accurate : '-');

            $lainLain = ($uj->jumlah_mel ?? 0) + ($uj->jumlah_pelancar ?? 0) + ($uj->jumlah_kawalan ?? 0) + ($uj->jumlah_parkir ?? 0);

            // Calculate total adjustment
            $ujAdjs = $this->adjustmentsByUjId[$uj->id] ?? collect();
            $ujAdjTotal = 0;
            foreach ($ujAdjs as $adj) {
                $nominal = (float) ($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                $jenisPeny = strtolower($adj->jenis_penyesuaian ?? '');
                if ($jenisPeny === 'penambahan') {
                    $ujAdjTotal += $nominal;
                } else {
                    $ujAdjTotal -= $nominal;
                }
            }

            $rows[] = [
                $index,
                $uj->tanggal_uang_jalan->format('d/m/Y'),
                $uj->nomor_uang_jalan,
                $noBukti,
                $sjNumber,
                $typeLabel,
                $namaBarang,
                $tujuanAmbil,
                $supir,
                $nik,
                $plat,
                (float) ($uj->jumlah_uang_jalan ?? 0),
                (float) ($uj->jumlah_mel ?? 0),
                (float) ($uj->jumlah_pelancar ?? 0),
                (float) ($uj->jumlah_kawalan ?? 0),
                (float) ($uj->jumlah_parkir ?? 0),
                (float) $lainLain,
                $ujAdjTotal != 0 ? (float) $ujAdjTotal : 0,
                (float) ($uj->jumlah_total ?? 0),
                $standalonePayment ? ($standalonePayment->keterangan ?: 'Pembayaran Aktivitas Lain') : '', // Keterangan Adj.
                $uj->createdBy->name ?? '-',
            ];

            // Add adjustment sub-rows
            if ($ujAdjs instanceof \Traversable || is_array($ujAdjs)) {
                foreach ($ujAdjs as $adj) {
                    $adjNominal = (float) ($adj->grand_total ?: ($adj->total ?: (isset($adj->jumlah) ? $adj->jumlah : 0)));
                    $adjJenis = strtolower($adj->jenis_penyesuaian ?? '');
                    $isPenambahan = ($adjJenis === 'penambahan');
                    $adjDate = $adj->tanggal_invoice ?? ($adj->tanggal ?? null);
                    $adjNomorInvoice = $adj->nomor_invoice ?? ($adj->nomor ?? '-');
                    $adjNomorBukti = $adj->_resolved_nomor_bukti ?? '-';
                    $adjLabel = ucfirst($adj->jenis_penyesuaian ?? 'Adjustment');

                    $displayNominal = $isPenambahan ? $adjNominal : -$adjNominal;

                    $rows[] = [
                        '',  // No
                        $adjDate ? \Carbon\Carbon::parse($adjDate)->format('d/m/Y') : '-', // Tanggal
                        $adjNomorInvoice, // Nomor UJ -> shows invoice number
                        $adjNomorBukti, // No. Bukti (Accurate)
                        '', // No. Surat Jalan
                        '', // Tipe
                        '', // Nama Barang
                        '', // Tujuan Ambil
                        '', // Supir
                        '', // NIK
                        '', // Plat Nomor
                        '', // Uang Jalan
                        '', // Mel
                        '', // Pelancar
                        '', // Kawalan
                        '', // Parkir
                        '', // Total Lain-lain
                        (float) $displayNominal, // Adj. UJ
                        '', // GRAND TOTAL
                        $adjLabel, // Keterangan Adj.
                        '', // Dibuat Oleh
                    ];

                    // Track adjustment row index for styling
                    $this->adjustmentRowIndices[] = $dataRowStart + count($rows) - 1;
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['REPORT RINCIAN UANG JALAN'],
            ['Periode: '.$this->startDate->format('d/m/Y').' s/d '.$this->endDate->format('d/m/Y')],
            [''],
            [
                'No',
                'Tanggal',
                'Nomor UJ',
                'No. Bukti (Accurate)',
                'No. Surat Jalan',
                'Tipe',
                'Nama Barang',
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
                'Adj. Uang Jalan',
                'GRAND TOTAL',
                'Keterangan Adj.',
                'Dibuat Oleh',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'U'; // 21 columns = A-U
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");

        $lastRow = $sheet->getHighestRow();

        $styles = [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B45309'], // Amber 700
                ],
            ],
            "A1:{$lastCol}{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];

        // Style adjustment rows with light background
        foreach ($this->adjustmentRowIndices as $rowIdx) {
            $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7'], // Light amber/yellow
                ],
                'font' => [
                    'italic' => true,
                    'size' => 9,
                ],
            ]);
        }

        return $styles;
    }
}
