<?php

namespace App\Exports;

use App\Models\TandaTerima;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TandaTerimaExport implements FromCollection, WithStyles, ShouldAutoSize
{
    protected $tandaTerimaIds;

    public function __construct(array $tandaTerimaIds)
    {
        $this->tandaTerimaIds = $tandaTerimaIds;
    }

    public function collection()
    {
        // Get tanda terimas with relations, grouped by nomor_ro
        $tandaTerimas = TandaTerima::with(['suratJalan'])
            ->whereIn('id', $this->tandaTerimaIds)
            ->orderBy('nomor_ro')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('nomor_ro');

        $rows = collect();

        // Process each RO group
        foreach ($tandaTerimas as $nomorRo => $groupedTandaTerimas) {
            // Add RO header row
            $rows->push([
                'RO NOMOR : ' . ($nomorRo ?: 'N/A'),
                '', '', '', '', '', '', '', '', ''
            ]);

            // Add column headers
            $rows->push([
                'CONTAINER_NO',
                'SIZE',
                'TIPE',
                'STATUS',
                'BERAT',
                'EXPIRED_DATE',
                'CONSIGNEE',
                'REMARK',
                'POD',
                'BOX_OPR'
            ]);

            // Add data for this RO
            foreach ($groupedTandaTerimas as $tandaTerima) {
                // Determine STATUS based on kegiatan
                $status = '';
                if ($tandaTerima->suratJalan) {
                    $kegiatan = strtolower($tandaTerima->kegiatan ?? '');
                    if (str_contains($kegiatan, 'isi') || str_contains($kegiatan, 'full')) {
                        $status = 'F';
                    } elseif (str_contains($kegiatan, 'kosong') || str_contains($kegiatan, 'empty')) {
                        $status = 'E';
                    }
                }

                // Determine POD based on tujuan_pengiriman
                $tujuanKirim = strtolower($tandaTerima->tujuan_pengiriman ?? '');
                $pod = '';
                if (str_contains($tujuanKirim, 'batam')) {
                    $pod = 'IDBTM';
                } elseif (str_contains($tujuanKirim, 'pinang')) {
                    $pod = 'IDKID';
                }

                // Get tipe kontainer from kontainers or stock_kontainers table
                $tipeKontainer = '';
                $noKontainer = $tandaTerima->no_kontainer;

                if ($noKontainer && strtoupper($noKontainer) !== 'CARGO') {
                    // Try to find in kontainers table first
                    $kontainer = Kontainer::where('nomor_seri_gabungan', $noKontainer)
                        ->orWhere(function($q) use ($noKontainer) {
                            $q->whereRaw("CONCAT(awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer) = ?", [$noKontainer]);
                        })
                        ->first();

                    if ($kontainer && $kontainer->tipe_kontainer) {
                        $tipeKontainer = strtoupper($kontainer->tipe_kontainer);
                    } else {
                        // If not found, try stock_kontainers table
                        $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $noKontainer)
                            ->orWhere(function($q) use ($noKontainer) {
                                $q->whereRaw("CONCAT(awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer) = ?", [$noKontainer]);
                            })
                            ->first();

                        if ($stockKontainer && $stockKontainer->tipe_kontainer) {
                            $tipeKontainer = strtoupper($stockKontainer->tipe_kontainer);
                        }
                    }

                    // Normalize tipe kontainer
                    if (str_contains($tipeKontainer, 'DRY')) {
                        $tipeKontainer = 'DRY';
                    } elseif (str_contains($tipeKontainer, 'REEFER')) {
                        $tipeKontainer = 'REEFER';
                    } elseif (str_contains($tipeKontainer, 'HC')) {
                        $tipeKontainer = 'HC';
                    } elseif (str_contains($tipeKontainer, 'FLAT')) {
                        $tipeKontainer = 'FLAT RACK';
                    } elseif (str_contains($tipeKontainer, 'OPEN')) {
                        $tipeKontainer = 'OPEN TOP';
                    }
                }

                // Default to DRY if not found or if CARGO
                if (empty($tipeKontainer)) {
                    $tipeKontainer = 'DRY';
                }

                // Format expired date (29/12/24 format)
                $expiredDate = '';
                if ($tandaTerima->expired_date) {
                    $expiredDate = $tandaTerima->expired_date->format('d/m/y');
                }

                $rows->push([
                    $tandaTerima->no_kontainer ?? '',    // CONTAINER_NO
                    $tandaTerima->size ?? '',            // SIZE
                    $tipeKontainer,                      // TIPE
                    $status,                             // STATUS (F/E)
                    $tandaTerima->tonase ?? '',          // BERAT
                    $expiredDate,                        // EXPIRED_DATE
                    '02522267',                          // CONSIGNEE (fixed value)
                    '',                                  // REMARK (kosong)
                    $pod,                                // POD (IDBTM/IDKID)
                    ''                                   // BOX_OPR (kosong)
                ]);
            }

            // Add empty row between RO groups
            $rows->push(['', '', '', '', '', '', '', '', '', '']);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Style for header rows (RO NOMOR and column headers)
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6FA'], // Light lavender background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Find and style all header rows
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            if (str_starts_with($cellValue, 'RO NOMOR :') ||
                in_array($cellValue, ['CONTAINER_NO', 'SIZE', 'TIPE', 'STATUS', 'BERAT', 'EXPIRED_DATE', 'CONSIGNEE', 'REMARK', 'POD', 'BOX_OPR'])) {
                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D3D3D3'], // Light gray background
                    ],
                ]);
            }
        }

        return [];
    }
}