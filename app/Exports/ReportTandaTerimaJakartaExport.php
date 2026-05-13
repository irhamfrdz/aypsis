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

class ReportTandaTerimaJakartaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
                // Extract items for perincian
                $items = [];
                if (!empty($item->dimensi_items)) {
                    $items = collect($item->dimensi_items)->map(fn($i) => [
                        'qty' => $i['jumlah'] ?? $i['qty'] ?? 0,
                        'satuan' => $i['satuan'] ?? '',
                        'nama' => $i['nama_barang'] ?? $i['nama'] ?? ''
                    ])->toArray();
                } else {
                    $items = [[
                        'qty' => $item->jumlah ?? 0,
                        'satuan' => $item->satuan ?? '',
                        'nama' => is_array($item->nama_barang) ? implode(', ', $item->nama_barang) : $item->nama_barang
                    ]];
                }

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
                    'perincian_items' => $items,
                ];
            });
        $data = $data->concat($ttStandard);

        // 2. Tanda Terima Tanpa Surat Jalan
        $ttTSJ = TandaTerimaTanpaSuratJalan::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->with(['dimensiItems'])
            ->get()
            ->map(function($item) {
                $items = $item->dimensiItems->map(fn($i) => [
                    'qty' => $i->jumlah ?? 0,
                    'satuan' => $i->satuan ?? '',
                    'nama' => $i->nama_barang ?? ''
                ])->toArray();

                if (empty($items)) {
                    $items = [[
                        'qty' => $item->jumlah_barang ?? 0,
                        'satuan' => $item->satuan_barang ?? '',
                        'nama' => $item->nama_barang ?? ''
                    ]];
                }

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
                    'perincian_items' => $items,
                ];
            });
        $data = $data->concat($ttTSJ);

        // 3. Tanda Terima LCL
        $ttLCL = TandaTerimaLcl::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->with(['tujuanKirim', 'kontainerPivot', 'items'])
            ->get()
            ->map(function($item) {
                $items = $item->items->map(fn($i) => [
                    'qty' => $i->jumlah ?? 0,
                    'satuan' => $i->satuan ?? '',
                    'nama' => $i->nama_barang ?? ''
                ])->toArray();

                return [
                    'source' => 'LCL',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $item->nomor_tanda_terima,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->nomor_kontainer,
                    'no_seal' => $item->nomor_seal,
                    'size' => $item->kontainerPivot->first()->size_kontainer ?? '-',
                    'pengirim' => $item->nama_pengirim,
                    'penerima' => $item->nama_penerima,
                    'tujuan' => $item->tujuanKirim?->nama_tujuan ?? '-',
                    'keterangan' => $item->kegiatan,
                    'perincian_items' => $items,
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
                    'tujuan' => $firstItem['tujuan'],
                ]);

                // Add Item Rows with numbering (01, 01a, 01b...)
                foreach ($items as $idx => $item) {
                    $number = sprintf('%02d', $groupCounter);
                    if ($idx > 0) {
                        $number .= chr(96 + $idx); // 1 -> a, 2 -> b, etc.
                    }
                    
                    // For each item in Tanda Terima, push its perincian
                    $perincian = $item['perincian_items'] ?? [];
                    if (empty($perincian)) {
                        $perincian = [['qty' => '', 'satuan' => '', 'nama' => '']];
                    }

                    foreach ($perincian as $pIdx => $pItem) {
                        $row = $item;
                        $row['type'] = 'item';
                        $row['display_number'] = ($pIdx === 0) ? $number : '';
                        
                        // Clear Tanda Terima info for additional perincian rows to keep it clean
                        if ($pIdx > 0) {
                            $row['tanggal'] = null;
                            $row['no_tt'] = '';
                            $row['no_sj_pabrik'] = '';
                            $row['pengirim'] = '';
                            $row['penerima'] = '';
                            $row['tujuan'] = '';
                            $row['keterangan'] = '';
                        }
                        
                        $row['p_qty'] = $pItem['qty'];
                        $row['p_satuan'] = $pItem['satuan'];
                        $row['p_nama'] = $pItem['nama'];
                        $finalData->push($row);
                    }
                }
                $groupCounter++;
            } else {
                // For items without container/seal info, just add them normally
                foreach ($items as $item) {
                    $perincian = $item['perincian_items'] ?? [['qty' => '', 'satuan' => '', 'nama' => '']];
                    foreach ($perincian as $pIdx => $pItem) {
                        $row = $item;
                        $row['type'] = 'item';
                        $row['display_number'] = '';
                        
                        if ($pIdx > 0) {
                            $row['tanggal'] = null;
                            $row['no_tt'] = '';
                            $row['no_sj_pabrik'] = '';
                            $row['pengirim'] = '';
                            $row['penerima'] = '';
                            $row['tujuan'] = '';
                            $row['keterangan'] = '';
                        }
                        
                        $row['p_qty'] = $pItem['qty'];
                        $row['p_satuan'] = $pItem['satuan'];
                        $row['p_nama'] = $pItem['nama'];
                        $finalData->push($row);
                    }
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
            'DESCRIPTION OF GOODS MANIFEST',
            '',
            '',
            '',
            '',
            '',
            'Qty',
            'Satuan',
            'Nama Barang',
            'Size',
            'SHIPPER',
            'CONSIGNEE',
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
                '', // K: Qty
                '', // L: Satuan
                '', // M: Nama Barang
                $row['size'], // N
                '', // O: Pengirim (Empty for header)
                '', // P: Penerima (Empty for header)
                $row['tujuan'], // Q (Empty for header)
                ''  // R: Keterangan (Empty for header)
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
            $row['p_qty'] ?? '', // K
            $row['p_satuan'] ?? '', // L
            $row['p_nama'] ?? '', // M
            $row['size'], // N
            $row['pengirim'], // O
            $row['penerima'], // P
            $row['tujuan'], // Q
            $row['keterangan'] // R
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set explicit widths to prevent "too large" columns
        $widths = [
            'A' => 15, 'B' => 15, 'C' => 20, 'D' => 20,
            'E' => 8,  'F' => 10, 'G' => 20, 'H' => 8,  'I' => 8,  'J' => 15,
            'K' => 10, 'L' => 12, 'M' => 50, // Perincian
            'N' => 10, 'O' => 25, 'P' => 25, 'Q' => 25, 'R' => 30 // Original trailing columns
        ];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Add one more row at the top for the "PERINCIAN" and "MANIFEST" headers
        $sheet->insertNewRowBefore(1, 1);
        
        // --- Row 1 & 2: Set Values and Merges ---
        // Column mapping for easy iteration
        $headerValues = [
            'A' => 'B/L NO',
            'B' => 'HS CODE',
            'C' => 'MARK AND NUMBERS',
            'D' => 'SEAL NO',
            'E' => 'MANIFEST', // Main header for manifest
            'K' => 'PERINCIAN', // Main header for perincian
            'N' => 'Size',
            'O' => 'SHIPPER',
            'P' => 'CONSIGNEE',
            'Q' => 'Tujuan',
            'R' => 'Keterangan'
        ];

        foreach ($headerValues as $col => $val) {
            $sheet->setCellValue("{$col}1", $val);
            if (!in_array($col, ['E', 'K'])) {
                $sheet->mergeCells("{$col}1:{$col}2");
            }
        }
        
        // Merges for the main headers
        $sheet->mergeCells('E1:J1');
        $sheet->mergeCells('K1:M1');
        
        // Row 2 Sub-headers
        $sheet->setCellValue('E2', 'DESCRIPTION OF GOODS MANIFEST');
        $sheet->mergeCells('E2:J2');
        
        $sheet->setCellValue('K2', 'DESCRIPTION OF GOODS');
        $sheet->mergeCells('K2:M2');

        // --- Apply Styles ---
        
        // Standard Headers (Light Gray)
        $sheet->getStyle('A1:D2')->applyFromArray($this->getStandardHeaderStyle());
        $sheet->getStyle('N1:R2')->applyFromArray($this->getStandardHeaderStyle());

        // Manifest Section (Light Green)
        $sheet->getStyle('E1:J2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92D050'],
            ],
        ]);

        // Perincian Section (Cyan/Blue)
        $sheet->getStyle('K1:M2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00B0F0'],
            ],
        ]);

        // General styling for all headers (Row 1 & 2)
        $sheet->getStyle('A1:R2')->getFont()->setBold(true);
        $sheet->getStyle('A1:R2')->getAlignment()->setWrapText(true);
        
        // Style for content data
        return [
            'A:R' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }

    protected function getStandardHeaderStyle()
    {
        return [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9'], // Light Gray
            ],
        ];
    }
}
