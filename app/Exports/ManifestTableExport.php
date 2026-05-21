<?php

namespace App\Exports;

use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManifestTableExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $manifests;

    protected $penerimaLookup = [];

    protected $termLookup = [];

    public function __construct($manifests)
    {
        $this->manifests = $manifests;
        $this->initializeLookups();
    }

    protected function initializeLookups()
    {
        // Fetch from Penerima model
        \App\Models\Penerima::all()->each(function ($p) {
            $name = strtoupper(trim($p->nama_penerima));
            if (! isset($this->penerimaLookup[$name])) {
                $this->penerimaLookup[$name] = [
                    'npwp' => $p->npwp,
                    'cp' => $p->contact_person,
                    'address' => $p->alamat,
                ];
            }
        });

        // Fetch from MasterPengirimPenerima (complement)
        \App\Models\MasterPengirimPenerima::all()->each(function ($p) {
            $name = strtoupper(trim($p->nama));
            if (! isset($this->penerimaLookup[$name])) {
                $this->penerimaLookup[$name] = [
                    'npwp' => $p->npwp,
                    'cp' => '',
                    'address' => $p->alamat,
                ];
            } else {
                if (empty($this->penerimaLookup[$name]['npwp'])) {
                    $this->penerimaLookup[$name]['npwp'] = $p->npwp;
                }
            }
        });

        // Fetch Terms
        \App\Models\Term::all()->each(function ($t) {
            $this->termLookup[$t->id] = $t->nama_status;
        });
    }

    protected function getSourceDocument($m)
    {
        // Try prospek first
        if ($m->prospek && $m->prospek->tanda_terima_id) {
            $tt = TandaTerima::find($m->prospek->tanda_terima_id);
            if ($tt) {
                return ['type' => 'Standard', 'model' => $tt];
            }
        }

        $ttNo = $m->nomor_tanda_terima;
        if (! $ttNo && $m->prospek) {
            // Fallback from prospek's no_surat_jalan or keterangan
            $ttNo = $m->prospek->no_surat_jalan;
            if (! $ttNo && preg_match('/Tanda Terima Tanpa Surat Jalan:\s*([^|]+)/', $m->prospek->keterangan, $matches)) {
                $ttNo = trim($matches[1]);
            }
        }

        if ($ttNo) {
            // Check FCL Tanda Terima
            $fcl = TandaTerima::where('no_surat_jalan', $ttNo)
                ->orWhere('no_dn', $ttNo)
                ->first();
            if ($fcl) {
                return ['type' => 'Standard', 'model' => $fcl];
            }

            // Check Tanda Terima Tanpa Surat Jalan
            $ttsj = TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $ttNo)
                ->orWhere('nomor_tanda_terima', $ttNo)
                ->first();
            if ($ttsj) {
                return ['type' => 'Tanpa SJ', 'model' => $ttsj];
            }

            // Check LCL Tanda Terima
            $lcl = TandaTerimaLcl::where('nomor_tanda_terima', $ttNo)->first();
            if ($lcl) {
                return ['type' => 'LCL', 'model' => $lcl];
            }
        }

        return null;
    }

    protected function getPpftzFromDocs($docs)
    {
        if (empty($docs)) {
            return '-';
        }
        if (is_string($docs)) {
            $docs = json_decode($docs, true);
        }
        if (! is_array($docs)) {
            return '-';
        }

        foreach ($docs as $doc) {
            if (is_string($doc) && (str_contains(strtoupper($doc), 'FTZ') || str_contains(strtoupper($doc), 'PPFTZ'))) {
                return $doc;
            }
        }

        return $docs[0] ?? '-';
    }

    public function collection()
    {
        $enhancedData = $this->manifests->map(function ($m) {
            // 1. Resolve source document
            $src = $this->getSourceDocument($m);
            $srcType = $src ? $src['type'] : null;
            $srcModel = $src ? $src['model'] : null;

            // 2. Fetch perincian items
            $perincian = [];
            if ($srcModel) {
                if ($srcType === 'Standard') {
                    $dimensi = ! empty($srcModel->dimensi_details) ? $srcModel->dimensi_details : $srcModel->dimensi_items;
                    if (! empty($dimensi)) {
                        $perincian = collect($dimensi)->map(function ($i) use ($srcModel) {
                            return [
                                'qty' => data_get($i, 'jumlah') ?? data_get($i, 'qty') ?? 0,
                                'satuan' => data_get($i, 'satuan') ?? '',
                                'nama' => data_get($i, 'nama_barang') ?: data_get($i, 'nama') ?: $srcModel->nama_barang ?: $srcModel->jenis_barang,
                                'weight' => data_get($i, 'tonase') ?? 0,
                                'meass' => data_get($i, 'meter_kubik') ?? 0,
                            ];
                        })->toArray();
                    }
                } elseif ($srcType === 'Tanpa SJ') {
                    if ($srcModel->dimensiItems && $srcModel->dimensiItems->isNotEmpty()) {
                        $perincian = $srcModel->dimensiItems->map(function ($i) use ($srcModel) {
                            return [
                                'qty' => $i->jumlah ?? 0,
                                'satuan' => $i->satuan ?? '',
                                'nama' => $i->nama_barang ?: $i->nama ?: $srcModel->nama_barang ?: $srcModel->jenis_barang,
                                'weight' => $i->tonase ?? 0,
                                'meass' => $i->meter_kubik ?? 0,
                            ];
                        })->toArray();
                    }
                } elseif ($srcType === 'LCL') {
                    if ($srcModel->items && $srcModel->items->isNotEmpty()) {
                        $perincian = $srcModel->items->map(function ($i) use ($srcModel) {
                            return [
                                'qty' => $i->jumlah ?? 0,
                                'satuan' => $i->satuan ?? '',
                                'nama' => $i->nama_barang ?: $i->nama ?: $srcModel->nama_barang ?: $srcModel->kegiatan ?: $srcModel->jenis_barang,
                                'weight' => $i->tonase ?? 0,
                                'meass' => $i->meter_kubik ?? 0,
                            ];
                        })->toArray();
                    }
                }
            }

            // Fallback to manifest's own fields if perincian is empty
            if (empty($perincian)) {
                $perincian = [[
                    'qty' => $m->kuantitas ?? '',
                    'satuan' => $m->satuan ?? '',
                    'nama' => $m->nama_barang ?? '',
                    'weight' => $m->tonnage ?? '',
                    'meass' => $m->volume ?? '',
                ]];
            }

            // Lookups
            $pName = strtoupper(trim($m->penerima));
            $pLookup = $this->penerimaLookup[$pName] ?? null;
            $sName = strtoupper(trim($m->pengirim));
            $sLookup = $this->penerimaLookup[$sName] ?? null;

            // Address & CP fallback
            $shipperAddress = $m->alamat_pengirim ?: ($sLookup['address'] ?? '-');
            $consigneeAddress = $m->alamat_penerima ?: ($pLookup['address'] ?? '-');
            $contactPerson = $m->contact_person ?: ($pLookup['cp'] ?? '-');
            $npwp = $pLookup['npwp'] ?? '-';

            $noSjPabrik = $srcModel ? ($srcModel->surat_jalan_pabrik ?? null) : null;
            $ppftz = $srcModel ? $this->getPpftzFromDocs($srcModel->dokumen_ppbj) : '-';

            // Determine LCL
            $isLcl = false;
            if (
                (! empty($m->tipe_kontainer) && stripos($m->tipe_kontainer, 'LCL') !== false) ||
                (! empty($m->size_kontainer) && stripos($m->size_kontainer, 'LCL') !== false)
            ) {
                $isLcl = true;
            }

            // Determine Cargo
            $isCargo = false;
            if (
                (! empty($m->tipe_kontainer) && stripos($m->tipe_kontainer, 'Cargo') !== false) ||
                (! empty($m->size_kontainer) && stripos($m->size_kontainer, 'Cargo') !== false)
            ) {
                $isCargo = true;
            }

            return [
                'model' => $m,
                'tanggal' => $m->tanggal_berangkat ?? $m->penerimaan ?? $m->created_at,
                'no_tt' => $m->nomor_tanda_terima_display,
                'no_sj_pabrik' => $noSjPabrik,
                'no_kontainer' => $m->nomor_kontainer,
                'no_seal' => $m->no_seal,
                'size' => $m->size_kontainer,
                'pengirim' => $m->pengirim,
                's_address' => $shipperAddress,
                'penerima' => $m->penerima,
                'p_address' => $consigneeAddress,
                'p_cp' => $contactPerson,
                'p_npwp' => $npwp,
                'ppftz' => $ppftz,
                'term' => $m->term,
                'tujuan' => $m->pelabuhan_tujuan ?: $m->ke,
                'keterangan' => $m->nama_barang,
                'is_lcl' => $isLcl,
                'is_cargo' => $isCargo,
                'perincian_items' => $perincian,
                'bl_no' => $m->nomor_bl,
            ];
        });

        // Sort data first to enable grouping
        $sortedData = $enhancedData->sortBy([
            [function ($item) {
                $hasContainer = ! empty($item['no_kontainer']) && $item['no_kontainer'] != '-';
                $hasSeal = ! empty($item['no_seal']) && $item['no_seal'] != '-';

                return ($hasContainer && $hasSeal) ? 0 : 1;
            }, 'asc'],
            ['is_lcl', 'asc'],
            ['no_kontainer', 'asc'],
            ['no_seal', 'asc'],
            ['tanggal', 'desc'],
        ]);

        $finalData = collect();
        $grouped = $sortedData->groupBy(function ($item) {
            return ($item['no_kontainer'] ?: 'none').'|'.($item['no_seal'] ?: 'none');
        });

        $groupCounter = 1;
        foreach ($grouped as $key => $items) {
            $firstItem = $items->first();
            $hasInfo = ! empty($firstItem['no_kontainer']) && $firstItem['no_kontainer'] != '-' &&
                       ! empty($firstItem['no_seal']) && $firstItem['no_seal'] != '-';

            if ($hasInfo) {
                $isStandard = ! $firstItem['is_lcl'];

                if (! $isStandard) {
                    // Add Header Row for the Group (Non-Standard like LCL)
                    $finalData->push([
                        'type' => 'header',
                        'no_kontainer' => $firstItem['no_kontainer'],
                        'no_seal' => $firstItem['no_seal'],
                        'size' => $firstItem['size'],
                        'tujuan' => $firstItem['tujuan'],
                    ]);
                }

                // Add Item Rows
                foreach ($items as $idx => $item) {
                    $number = sprintf('%02d', $groupCounter);
                    if ($idx > 0) {
                        $number .= chr(96 + $idx); // 1 -> a, 2 -> b, etc.
                    }

                    $perincian = $item['perincian_items'] ?? [];
                    if (empty($perincian)) {
                        $perincian = [['qty' => '', 'satuan' => '', 'nama' => '', 'weight' => '', 'meass' => '']];
                    }

                    // Special case for Standard: condense all items into 1 row
                    if (! $item['is_lcl'] && count($perincian) > 1) {
                        $pItem = [
                            'qty' => implode("\n", array_column($perincian, 'qty')),
                            'satuan' => implode("\n", array_column($perincian, 'satuan')),
                            'nama' => implode("\n", array_column($perincian, 'nama')),
                            'weight' => implode("\n", array_column($perincian, 'weight')),
                            'meass' => implode("\n", array_column($perincian, 'meass')),
                        ];
                        $perincian = [$pItem];
                    }

                    foreach ($perincian as $pIdx => $pItem) {
                        $row = $item;
                        $row['type'] = 'item';
                        $row['display_number'] = ($pIdx === 0) ? $number : '';

                        // If Standard, we inject the container header info into the first perincian row
                        if (! $item['is_lcl'] && $pIdx === 0) {
                            $row['is_combined_standard'] = true;
                        }

                        // Clear some fields for additional perincian rows to keep it clean
                        if ($pIdx > 0) {
                            $row['tanggal'] = null;
                            $row['no_tt'] = '';
                            $row['no_sj_pabrik'] = '';
                            $row['pengirim'] = '';
                            $row['penerima'] = '';
                            $row['tujuan'] = '';
                            $row['keterangan'] = '';
                            $row['bl_no'] = '';
                        }

                        $row['p_qty'] = $pItem['qty'];
                        $row['p_satuan'] = $pItem['satuan'];
                        $row['p_nama'] = $pItem['nama'];
                        $row['p_weight'] = $pItem['weight'];
                        $row['p_meass'] = $pItem['meass'];
                        $finalData->push($row);
                    }
                }
                $groupCounter++;
            } else {
                // For items without container/seal info (e.g. Cargo), just add them normally
                foreach ($items as $item) {
                    $perincian = $item['perincian_items'] ?? [['qty' => '', 'satuan' => '', 'nama' => '', 'weight' => '', 'meass' => '']];

                    if (! $item['is_lcl'] && count($perincian) > 1) {
                        $pItem = [
                            'qty' => implode("\n", array_column($perincian, 'qty')),
                            'satuan' => implode("\n", array_column($perincian, 'satuan')),
                            'nama' => implode("\n", array_column($perincian, 'nama')),
                            'weight' => implode("\n", array_column($perincian, 'weight')),
                            'meass' => implode("\n", array_column($perincian, 'meass')),
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
                            $row['bl_no'] = '';
                        }

                        $row['p_qty'] = $pItem['qty'];
                        $row['p_satuan'] = $pItem['satuan'];
                        $row['p_nama'] = $pItem['nama'];
                        $row['p_weight'] = $pItem['weight'];
                        $row['p_meass'] = $pItem['meass'];
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
            'TANGGAL',
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
            'Weight',
            'Meass',
            'Size',
            'SHIPPER',
            'Shipper Address',
            'CONSIGNEE',
            'Consignee Address',
            'NPWP',
            'Contact Person',
            'Document PPFTZ',
            'TERM',
            'Tujuan',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        $formattedDate = ! empty($row['tanggal']) ? (is_string($row['tanggal']) ? Carbon::parse($row['tanggal'])->format('d/m/Y') : $row['tanggal']->format('d/m/Y')) : '';

        if ($row['type'] === 'header') {
            return [
                '', // A: TANGGAL
                '', // B: NO. TANDA TERIMA / SJ
                '', // C: B/L NO
                '', // D: HS CODE
                $row['no_kontainer'], // E: MARK AND NUMBERS
                $row['no_seal'], // F: SEAL NO
                '1', // G: Qty
                'Unit', // H: Satuan
                'Container '.($row['size'] ?: '-').' feet stc :', // I: Desc
                '', // J
                '', // K
                'General cargo', // L: Activity
                '', // M: Qty
                '', // N: Satuan
                '', // O: Nama Barang
                '', // P: Weight
                '', // Q: Meass
                $row['size'], // R: Size
                '', // S: SHIPPER
                '', // T: Shipper Address
                '', // U: CONSIGNEE
                '', // V: Address
                '', // W: NPWP
                '', // X: Contact Person
                '', // Y: Document PPFTZ
                '', // Z: TERM
                $row['tujuan'], // AA
                '',  // AB: Keterangan
            ];
        }

        // Item row
        $data = [
            $formattedDate, // A: TANGGAL
            $row['no_tt'], // B: NO. TANDA TERIMA / SJ
            $row['bl_no'], // C: B/L NO
            '', // D: HS CODE
            '', // E: MARK AND NUMBERS
            '', // F: SEAL NO
            '', // G: Qty
            '', // H: Satuan
            '', // I: Desc
            '', // J
            '', // K
            '', // L: Activity
            $row['p_qty'] ?? '', // M: Qty
            $row['p_satuan'] ?? '', // N: Satuan
            $row['p_nama'] ?? '', // O: Nama Barang
            $row['p_weight'] ?? '', // P: Weight
            $row['p_meass'] ?? '', // Q: Meass
            $row['size'], // R: Size
            $row['pengirim'], // S: SHIPPER
            $row['s_address'] ?? '-', // T: Shipper Address
            $row['penerima'], // U: CONSIGNEE
            $row['p_address'] ?? '-', // V: Address
            $row['p_npwp'] ?? '-', // W: NPWP
            $row['p_cp'] ?? '-', // X: Contact Person
            $row['ppftz'] ?? '-', // Y: Document PPFTZ
            $row['term'] ?? '-', // Z: TERM
            $row['tujuan'], // AA: Tujuan
            $row['keterangan'], // AB: Keterangan
        ];

        // If it's a combined standard row, inject the container header values
        if (! empty($row['is_combined_standard'])) {
            $data[4] = $row['no_kontainer']; // E: MARK AND NUMBERS
            $data[5] = $row['no_seal']; // F: SEAL NO
            $data[6] = '1'; // G: Qty
            $data[7] = 'Unit'; // H: Satuan
            $data[8] = 'Container '.($row['size'] ?: '-').' feet stc :'; // I: Desc
            $data[11] = 'General cargo'; // L: Activity
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Set explicit widths to prevent "too large" columns
        $widths = [
            'A' => 15, // Tanggal
            'B' => 25, // No. TT / SJ
            'C' => 15, 'D' => 15, 'E' => 20, 'F' => 20,
            'G' => 8,  'H' => 10, 'I' => 20, 'J' => 8,  'K' => 8,  'L' => 15,
            'M' => 10, 'N' => 12, 'O' => 40, 'P' => 12, 'Q' => 12, // Perincian
            'R' => 10, 'S' => 25, 'T' => 35, 'U' => 25, // Size, Shipper, Shipper Address, Consignee
            'V' => 40, 'W' => 20, 'X' => 20, 'Y' => 20, 'Z' => 15, // Consignee Address, NPWP, CP, PPFTZ, TERM
            'AA' => 25, 'AB' => 30, // Tujuan, Keterangan
        ];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Add one more row at the top for the "PERINCIAN" and "MANIFEST" headers
        $sheet->insertNewRowBefore(1, 1);

        // --- Row 1 & 2: Set Values and Merges ---
        $headerValues = [
            'A' => 'TANGGAL',
            'B' => 'NO. TANDA TERIMA / SJ',
            'C' => 'B/L NO',
            'D' => 'HS CODE',
            'E' => 'MARK AND NUMBERS',
            'F' => 'SEAL NO',
            'G' => 'MANIFEST', // Main header for manifest
            'M' => 'PERINCIAN', // Main header for perincian
            'R' => 'Size',
            'S' => 'SHIPPER',
            'T' => 'Shipper Address',
            'U' => 'CONSIGNEE',
            'V' => 'Consignee Address',
            'W' => 'NPWP',
            'X' => 'Contact Person',
            'Y' => 'Document PPFTZ',
            'Z' => 'TERM',
            'AA' => 'Tujuan',
            'AB' => 'Keterangan',
        ];

        foreach ($headerValues as $col => $val) {
            $sheet->setCellValue("{$col}1", $val);
            if (! in_array($col, ['G', 'M'])) {
                $sheet->mergeCells("{$col}1:{$col}2");
            }
        }

        // Merges for the main headers
        $sheet->mergeCells('G1:L1');
        $sheet->mergeCells('M1:Q1');

        // Row 2 Sub-headers
        $sheet->setCellValue('G2', 'Qty');
        $sheet->setCellValue('H2', 'Satuan');
        $sheet->setCellValue('I2', 'DESCRIPTION OF GOODS MANIFEST');
        $sheet->mergeCells('I2:L2');

        $sheet->setCellValue('M2', 'Qty');
        $sheet->setCellValue('N2', 'Satuan');
        $sheet->setCellValue('O2', 'Nama Barang');
        $sheet->setCellValue('P2', 'Weight');
        $sheet->setCellValue('Q2', 'Meass');

        // --- Apply Styles ---

        // Standard Headers (Light Gray)
        $sheet->getStyle('A1:F2')->applyFromArray($this->getStandardHeaderStyle());
        $sheet->getStyle('R1:U2')->applyFromArray($this->getStandardHeaderStyle()); // Size to Consignee
        $sheet->getStyle('V1:Z2')->applyFromArray($this->getStandardHeaderStyle()); // New columns
        $sheet->getStyle('AA1:AB2')->applyFromArray($this->getStandardHeaderStyle()); // End columns

        // Manifest Section (Light Green)
        $sheet->getStyle('G1:L2')->applyFromArray([
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
        $sheet->getStyle('M1:Q2')->applyFromArray([
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
        $sheet->getStyle('A1:AB2')->getFont()->setBold(true);
        $sheet->getStyle('A1:AB2')->getAlignment()->setWrapText(true);

        // Add borders to all active cells
        $lastRow = $sheet->getHighestRow();
        $range = 'A1:AB'.$lastRow;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Style for content data
        return [
            'A:AB' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true,
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
