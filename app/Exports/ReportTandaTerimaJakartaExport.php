<?php

namespace App\Exports;

use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaBongkaran;
use App\Models\TandaTerimaLcl;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportTandaTerimaJakartaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data = collect();

        // 1. Tanda Terima (Standard)
        $ttStandard = TandaTerima::whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($item) {
                return [
                    'source' => 'Standard',
                    'tanggal' => $item->tanggal,
                    'no_tt' => $item->no_surat_jalan ?? $item->surat_jalan?->no_surat_jalan ?? '-',
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size,
                    'pengirim' => $item->pengirim,
                    'penerima' => $item->penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->kegiatan,
                ];
            });
        $data = $data->concat($ttStandard);

        // 2. Tanda Terima Tanpa Surat Jalan
        $ttTSJ = TandaTerimaTanpaSuratJalan::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->get()
            ->map(function($item) {
                return [
                    'source' => 'Tanpa SJ',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $item->no_tanda_terima,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size_kontainer,
                    'pengirim' => $item->pengirim,
                    'penerima' => $item->penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->aktifitas,
                ];
            });
        $data = $data->concat($ttTSJ);

        // 3. Tanda Terima LCL
        $ttLCL = TandaTerimaLcl::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->with(['tujuanKirim'])
            ->get()
            ->map(function($item) {
                return [
                    'source' => 'LCL',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $item->nomor_tanda_terima,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->nomor_kontainer,
                    'no_seal' => $item->nomor_seal,
                    'size' => '-',
                    'pengirim' => $item->nama_pengirim,
                    'penerima' => $item->nama_penerima,
                    'tujuan' => $item->tujuanKirim?->nama_tujuan ?? '-',
                    'keterangan' => $item->kegiatan,
                ];
            });
        $data = $data->concat($ttLCL);

        // Sort data first to enable grouping
        $sortedData = $data->sortBy([
            [function($item) {
                $hasContainer = !empty($item['no_kontainer']) && $item['no_kontainer'] != '-';
                $hasSeal = !empty($item['no_seal']) && $item['no_seal'] != '-';
                return ($hasContainer && $hasSeal) ? 0 : 1;
            }, 'asc'],
            ['source', 'asc'],
            ['no_kontainer', 'asc'],
            ['no_seal', 'asc'],
            ['tanggal', 'desc'],
        ]);

        // Transform collection to include header rows and special numbering
        $finalData = collect();
        $grouped = $sortedData->groupBy(function($item) {
            return ($item['no_kontainer'] ?: 'none') . '|' . ($item['no_seal'] ?: 'none');
        });

        $groupCounter = 1;
        foreach ($grouped as $key => $items) {
            $firstItem = $items->first();
            $hasInfo = !empty($firstItem['no_kontainer']) && $firstItem['no_kontainer'] != '-' && 
                       !empty($firstItem['no_seal']) && $firstItem['no_seal'] != '-';

            if ($hasInfo) {
                // Add Header Row for the Group
                $finalData->push([
                    'type' => 'header',
                    'no_kontainer' => $firstItem['no_kontainer'],
                    'no_seal' => $firstItem['no_seal'],
                    'size' => $firstItem['size'],
                ]);

                // Add Item Rows with numbering (01, 01a, 01b...)
                foreach ($items as $idx => $item) {
                    $number = sprintf('%02d', $groupCounter);
                    if ($idx > 0) {
                        $number .= chr(96 + $idx); // 1 -> a, 2 -> b, etc.
                    }
                    
                    $item['type'] = 'item';
                    $item['display_number'] = $number;
                    $finalData->push($item);
                }
                $groupCounter++;
            } else {
                // For items without container/seal info, just add them normally
                foreach ($items as $item) {
                    $item['type'] = 'item';
                    $item['display_number'] = ''; // Or some other numbering?
                    $finalData->push($item);
                }
            }
        }

        return $finalData;
    }

    public function headings(): array
    {
        return [
            'B/L NO',
            'HS CODE',
            'MARK AND NUMBERS',
            'SEAL NO',
            'DESCRIPTION OF GOODS',
            '',
            '',
            '',
            '',
            '',
            'Size',
            'Pengirim',
            'Penerima',
            'Tujuan',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        if ($row['type'] === 'header') {
            return [
                '', // A: B/L NO
                '', // B: HS CODE
                $row['no_kontainer'], // C: MARK AND NUMBERS
                $row['no_seal'], // D: SEAL NO
                '1', // E: Qty
                'Unit', // F: Satuan
                'Container ' . ($row['size'] ?: '-') . ' feet stc :', // G: Desc
                '', // H
                '', // I
                'General cargo', // J
                $row['size'], // K
                '', // L: Pengirim (Empty for header)
                '', // M: Penerima (Empty for header)
                '', // N: Tujuan (Empty for header)
                ''  // O: Keterangan (Empty for header)
            ];
        }

        // Item row
        return [
            '', // A: B/L NO
            '', // B: HS CODE
            '', // C: MARK AND NUMBERS (Removed numbering as requested)
            '', // D: SEAL NO (Empty for item)
            '', // E: Qty (Empty for item)
            '', // F: Satuan (Empty for item)
            '', // G: Desc (Empty for item)
            '', // H
            '', // I
            '', // J
            $row['size'], // K
            $row['pengirim'], // L
            $row['penerima'], // M
            $row['tujuan'], // N
            $row['keterangan'] // O
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('E1:J1');
        
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '92D050'], // Light Green from image
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'A:Z' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }
}
