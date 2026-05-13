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
    protected $penerimaLookup = [];
    protected $termLookup = [];

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->initializeLookups();
    }

    protected function initializeLookups()
    {
        // Fetch from Penerima model
        \App\Models\Penerima::all()->each(function($p) {
            $name = strtoupper(trim($p->nama_penerima));
            if (!isset($this->penerimaLookup[$name])) {
                $this->penerimaLookup[$name] = [
                    'npwp' => $p->npwp,
                    'cp' => $p->contact_person,
                    'address' => $p->alamat
                ];
            }
        });

        // Fetch from MasterPengirimPenerima (complement)
        \App\Models\MasterPengirimPenerima::all()->each(function($p) {
            $name = strtoupper(trim($p->nama));
            if (!isset($this->penerimaLookup[$name])) {
                $this->penerimaLookup[$name] = [
                    'npwp' => $p->npwp,
                    'cp' => '',
                    'address' => $p->alamat
                ];
            } else {
                // If already exists but NPWP is empty, try to fill it
                if (empty($this->penerimaLookup[$name]['npwp'])) {
                    $this->penerimaLookup[$name]['npwp'] = $p->npwp;
                }
            }
        });

        // Fetch Terms
        \App\Models\Term::all()->each(function($t) {
            $this->termLookup[$t->id] = $t->nama_status;
        });
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
                $dimensi = !empty($item->dimensi_details) ? $item->dimensi_details : $item->dimensi_items;

                if (!empty($dimensi)) {
                    $items = collect($dimensi)->map(function($i) use ($item) {
                        $qty = data_get($i, 'jumlah') ?? data_get($i, 'qty') ?? 0;
                        $satuan = data_get($i, 'satuan') ?? '';
                        $nama = data_get($i, 'nama_barang') ?: data_get($i, 'nama') ?: $item->nama_barang ?: $item->jenis_barang;
                        
                        // Handle if $item->nama_barang was an array (unlikely but for safety)
                        if (is_array($nama)) {
                            $nama = implode(', ', $nama);
                        }

                        return [
                            'qty' => $qty,
                            'satuan' => $satuan,
                            'nama' => $nama
                        ];
                    })->toArray();
                } else {
                    $items = [[
                        'qty' => $item->jumlah ?? 0,
                        'satuan' => $item->satuan ?? '',
                        'nama' => !empty($item->nama_barang) ? (is_array($item->nama_barang) ? implode(', ', $item->nama_barang) : $item->nama_barang) : $item->jenis_barang
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
                    'address_raw' => $item->alamat_penerima,
                    'cp_raw' => $item->pic_penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->kegiatan,
                    'ppftz' => $this->getPpftzFromDocs($item->dokumen_ppbj),
                    'term' => $item->term,
                    'perincian_items' => $items,
                ];
            });
        $data = $data->concat($ttStandard);

        // 2. Tanda Terima Tanpa Surat Jalan
        $ttTSJ = TandaTerimaTanpaSuratJalan::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->with(['dimensiItems'])
            ->get()
            ->map(function($item) {
                $items = $item->dimensiItems->map(function($i) use ($item) {
                    $nama = $i->nama_barang ?: $i->nama ?: $item->nama_barang ?: $item->jenis_barang;
                    return [
                        'qty' => $i->jumlah ?? 0,
                        'satuan' => $i->satuan ?? '',
                        'nama' => $nama
                    ];
                })->toArray();

                if (empty($items)) {
                    $nama = $item->nama_barang;
                    if (empty($nama)) {
                        $nama = $item->jenis_barang;
                    }
                    $items = [[
                        'qty' => $item->jumlah_barang ?? 0,
                        'satuan' => $item->satuan_barang ?? '',
                        'nama' => $nama
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
                    'address_raw' => $item->alamat_penerima,
                    'cp_raw' => $item->pic_penerima ?: $item->pic,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->aktifitas,
                    'ppftz' => $this->getPpftzFromDocs($item->dokumen_ppbj),
                    'term' => $this->termLookup[$item->term_id] ?? '-',
                    'perincian_items' => $items,
                ];
            });
        $data = $data->concat($ttTSJ);

        // 3. Tanda Terima LCL
        $ttLCL = TandaTerimaLcl::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->with(['tujuanKirim', 'kontainerPivot', 'items'])
            ->get()
            ->map(function($item) {
                $items = $item->items->map(function($i) use ($item) {
                    $nama = $i->nama_barang ?: $i->nama ?: $item->nama_barang ?: $item->kegiatan ?: $item->jenis_barang;
                    return [
                        'qty' => $i->jumlah ?? 0,
                        'satuan' => $i->satuan ?? '',
                        'nama' => $nama
                    ];
                })->toArray();

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
                    'address_raw' => $item->alamat_penerima,
                    'cp_raw' => $item->pic_penerima,
                    'tujuan' => $item->tujuanKirim?->nama_tujuan ?? '-',
                    'keterangan' => $item->kegiatan,
                    'ppftz' => $this->getPpftzFromDocs($item->dokumen_ppbj),
                    'term' => $this->termLookup[$item->term_id] ?? '-',
                    'perincian_items' => $items,
                ];
            });
        $data = $data->concat($ttLCL);

        // Enhance with lookup data
        $enhancedData = $data->map(function($item) {
            $name = strtoupper(trim($item['penerima']));
            $lookup = $this->penerimaLookup[$name] ?? null;
            
            // Prioritize address and CP from the record itself, fallback to lookup
            $item['p_address'] = $item['address_raw'] ?: ($lookup['address'] ?? '-');
            $item['p_cp'] = $item['cp_raw'] ?: ($lookup['cp'] ?? '-');
            
            // NPWP usually only in lookup
            $item['p_npwp'] = $lookup['npwp'] ?? '-';
            
            return $item;
        });

        // Sort data first to enable grouping
        $sortedData = $enhancedData->sortBy([
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
                // Special check: If this group is Standard, we don't want a separate header row
                // We will merge container info into the item row itself
                $isStandard = ($firstItem['source'] === 'Standard');

                if (!$isStandard) {
                    // Add Header Row for the Group (Non-Standard like LCL)
                    $finalData->push([
                        'type' => 'header',
                        'no_kontainer' => $firstItem['no_kontainer'],
                        'no_seal' => $firstItem['no_seal'],
                        'size' => $firstItem['size'],
                        'tujuan' => $firstItem['tujuan'],
                    ]);
                }

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

                    // Special case for Standard: condense all items into 1 row
                    if ($item['source'] === 'Standard' && count($perincian) > 1) {
                        $pItem = [
                            'qty' => implode("\n", array_column($perincian, 'qty')),
                            'satuan' => implode("\n", array_column($perincian, 'satuan')),
                            'nama' => implode("\n", array_column($perincian, 'nama')),
                        ];
                        $perincian = [$pItem];
                    }

                    foreach ($perincian as $pIdx => $pItem) {
                        $row = $item;
                        $row['type'] = 'item';
                        $row['display_number'] = ($pIdx === 0) ? $number : '';
                        
                        // If Standard, we inject the container header info into the first perincian row
                        if ($item['source'] === 'Standard' && $pIdx === 0) {
                            $row['is_combined_standard'] = true;
                        }

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
                    
                    // Special case for Standard: condense all items into 1 row
                    if ($item['source'] === 'Standard' && count($perincian) > 1) {
                        $pItem = [
                            'qty' => implode("\n", array_column($perincian, 'qty')),
                            'satuan' => implode("\n", array_column($perincian, 'satuan')),
                            'nama' => implode("\n", array_column($perincian, 'nama')),
                        ];
                        $perincian = [$pItem];
                    }

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
            'NO. TANDA TERIMA / SJ',
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
            'Consignee Address',
            'NPWP',
            'Contact Person',
            'Document PPFTZ',
            'TERM',
            'Tujuan',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        if ($row['type'] === 'header') {
            return [
                '', // A: NO. TANDA TERIMA / SJ
                '', // B: B/L NO
                '', // C: HS CODE
                $row['no_kontainer'], // D: MARK AND NUMBERS
                $row['no_seal'], // E: SEAL NO
                '1', // F: Qty
                'Unit', // G: Satuan
                'Container ' . ($row['size'] ?: '-') . ' feet stc :', // H: Desc
                '', // I
                '', // J
                'General cargo', // K: Activity
                '', // L: Qty
                '', // M: Satuan
                '', // N: Nama Barang
                $row['size'], // O: Size
                '', // P: SHIPPER
                '', // Q: CONSIGNEE
                '', // R: Address
                '', // S: NPWP
                '', // T: Contact Person
                '', // U: Document PPFTZ
                '', // V: TERM
                $row['tujuan'], // W
                ''  // X: Keterangan
            ];
        }

        // Item row
        $data = [
            $row['no_tt'], // A: NO. TANDA TERIMA / SJ
            '', // B: B/L NO
            '', // C: HS CODE
            '', // D: MARK AND NUMBERS
            '', // E: SEAL NO
            '', // F: Qty
            '', // G: Satuan
            '', // H: Desc
            '', // I
            '', // J
            '', // K: Activity
            $row['p_qty'] ?? '', // L: Qty
            $row['p_satuan'] ?? '', // M: Satuan
            $row['p_nama'] ?? '', // N: Nama Barang
            $row['size'], // O: Size
            $row['pengirim'], // P: SHIPPER
            $row['penerima'], // Q: CONSIGNEE
            $row['p_address'] ?? '-', // R: Address
            $row['p_npwp'] ?? '-', // S: NPWP
            $row['p_cp'] ?? '-', // T: Contact Person
            $row['ppftz'] ?? '-', // U: Document PPFTZ
            $row['term'] ?? '-', // V: TERM
            $row['tujuan'], // W: Tujuan
            $row['keterangan'] // X: Keterangan
        ];

        // If it's a combined standard row, inject the container header values
        if (!empty($row['is_combined_standard'])) {
            $data[3] = $row['no_kontainer']; // D: MARK AND NUMBERS
            $data[4] = $row['no_seal']; // E: SEAL NO
            $data[5] = '1'; // F: Qty
            $data[6] = 'Unit'; // G: Satuan
            $data[7] = 'Container ' . ($row['size'] ?: '-') . ' feet stc :'; // H: Desc
            $data[10] = 'General cargo'; // K: Activity
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Set explicit widths to prevent "too large" columns
        $widths = [
            'A' => 25, // No. TT / SJ
            'B' => 15, 'C' => 15, 'D' => 20, 'E' => 20,
            'F' => 8,  'G' => 10, 'H' => 20, 'I' => 8,  'J' => 8,  'K' => 15,
            'L' => 10, 'M' => 12, 'N' => 50, // Perincian
            'O' => 10, 'P' => 25, 'Q' => 25, // Size, Shipper, Consignee
            'R' => 40, 'S' => 20, 'T' => 20, 'U' => 20, 'V' => 15, // Address, NPWP, CP, PPFTZ, TERM
            'W' => 25, 'X' => 30 // Tujuan, Keterangan
        ];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Add one more row at the top for the "PERINCIAN" and "MANIFEST" headers
        $sheet->insertNewRowBefore(1, 1);
        
        // --- Row 1 & 2: Set Values and Merges ---
        // Column mapping for easy iteration
        $headerValues = [
            'A' => 'NO. TANDA TERIMA / SJ',
            'B' => 'B/L NO',
            'C' => 'HS CODE',
            'D' => 'MARK AND NUMBERS',
            'E' => 'SEAL NO',
            'F' => 'MANIFEST', // Main header for manifest
            'L' => 'PERINCIAN', // Main header for perincian
            'O' => 'Size',
            'P' => 'SHIPPER',
            'Q' => 'CONSIGNEE',
            'R' => 'Consignee Address',
            'S' => 'NPWP',
            'T' => 'Contact Person',
            'U' => 'Document PPFTZ',
            'V' => 'TERM',
            'W' => 'Tujuan',
            'X' => 'Keterangan'
        ];

        foreach ($headerValues as $col => $val) {
            $sheet->setCellValue("{$col}1", $val);
            if (!in_array($col, ['F', 'L'])) {
                $sheet->mergeCells("{$col}1:{$col}2");
            }
        }
        
        // Merges for the main headers
        $sheet->mergeCells('F1:K1');
        $sheet->mergeCells('L1:N1');
        
        // Row 2 Sub-headers
        $sheet->setCellValue('F2', 'DESCRIPTION OF GOODS MANIFEST');
        $sheet->mergeCells('F2:K2');
        
        $sheet->setCellValue('L2', 'DESCRIPTION OF GOODS');
        $sheet->mergeCells('L2:N2');

        // --- Apply Styles ---
        
        // Standard Headers (Light Gray)
        $sheet->getStyle('A1:E2')->applyFromArray($this->getStandardHeaderStyle());
        $sheet->getStyle('O1:Q2')->applyFromArray($this->getStandardHeaderStyle()); // Size to Consignee
        $sheet->getStyle('R1:V2')->applyFromArray($this->getStandardHeaderStyle()); // New columns
        $sheet->getStyle('W1:X2')->applyFromArray($this->getStandardHeaderStyle()); // End columns

        // Manifest Section (Light Green)
        $sheet->getStyle('F1:K2')->applyFromArray([
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
        $sheet->getStyle('L1:N2')->applyFromArray([
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
        $sheet->getStyle('A1:X2')->getFont()->setBold(true);
        $sheet->getStyle('A1:X2')->getAlignment()->setWrapText(true);
        
        // Style for content data
        return [
            'A:X' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    protected function getPpftzFromDocs($docs)
    {
        if (empty($docs)) return '-';
        if (is_string($docs)) {
            $docs = json_decode($docs, true);
        }
        if (!is_array($docs)) return '-';
        
        // Look for common PPFTZ identifiers in PPBJ or other docs
        foreach ($docs as $doc) {
            if (is_string($doc) && (str_contains(strtoupper($doc), 'FTZ') || str_contains(strtoupper($doc), 'PPFTZ'))) {
                return $doc;
            }
        }
        
        // Fallback to first document if exists
        return $docs[0] ?? '-';
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
