<?php

namespace App\Exports;

use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManifestTableExport implements FromCollection, WithCustomStartCell, WithMapping, WithStyles
{
    protected $manifests;

    protected $penerimaLookup = [];

    protected $termLookup = [];

    public function __construct($manifests)
    {
        $this->manifests = $manifests;
        $this->initializeLookups();
    }

    public function startCell(): string
    {
        return 'A10';
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

        // Sort data: cargo last, and others sorted naturally by bl_no
        $sortedData = $enhancedData->sort(function ($a, $b) {
            $isCargoA = $a['is_cargo'];
            $isCargoB = $b['is_cargo'];
            if ($isCargoA && ! $isCargoB) {
                return 1;
            }
            if (! $isCargoA && $isCargoB) {
                return -1;
            }

            return strnatcasecmp($a['bl_no'] ?? '', $b['bl_no'] ?? '');
        });

        // Group by bl_no (with unique fallback for empty values to prevent merging)
        $grouped = $sortedData->groupBy(function ($item) {
            return $item['bl_no'] ?: ('empty_'.$item['model']->id);
        });

        $finalData = collect();
        $groupCounter = 1;

        foreach ($grouped as $key => $items) {
            $firstItem = $items->first();
            $hasInfo = ! empty($firstItem['no_kontainer']) && $firstItem['no_kontainer'] != '-';

            $groupNumber = '';
            if ($hasInfo && ! $firstItem['is_cargo']) {
                $groupNumber = sprintf('%02d', $groupCounter);
                $groupCounter++;
            }

            $itemsCount = count($items);
            foreach ($items as $idx => $item) {
                $perincian = $item['perincian_items'] ?? [];
                if (empty($perincian)) {
                    $perincian = [['qty' => '', 'satuan' => '', 'nama' => '', 'weight' => '', 'meass' => '']];
                }

                // Condense perincian details into 1 row for FCL
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

                    // Group-level details are only written on the first row of the group
                    $isFirstOfGroup = ($idx === 0 && $pIdx === 0);
                    if ($isFirstOfGroup) {
                        $row['group_number'] = $groupNumber;
                        $row['show_group_fields'] = true;
                        $row['group_count'] = $itemsCount;
                    } else {
                        $row['group_number'] = '';
                        $row['show_group_fields'] = false;
                        $row['group_count'] = null;
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

        // Add Grand Total row
        $lastDataRow = 9 + $finalData->count();
        $sizes = $this->manifests->pluck('size_kontainer')->filter()->unique();
        $sizeStr = $sizes->implode(' & ');
        $containerText = 'Container '.($sizeStr ?: '20').' feet';

        $finalData->push([
            'type' => 'total',
            'qty_formula' => "=SUM(F10:F{$lastDataRow})",
            'container_text' => $containerText,
            'weight_formula' => "=SUM(O10:O{$lastDataRow})",
            'volume_formula' => "=SUM(P10:P{$lastDataRow})",
        ]);

        // Add Signatures rows
        $firstManifest = $this->manifests->first();
        $pelabuhanAsal = strtoupper($firstManifest->pelabuhan_asal ?: 'SUNDA KELAPA');
        Carbon::setLocale('id');
        $dateStr = $firstManifest && $firstManifest->tanggal_berangkat
            ? strtoupper(Carbon::parse($firstManifest->tanggal_berangkat)->translatedFormat('d F Y'))
            : strtoupper(Carbon::now()->translatedFormat('d F Y'));

        $finalData->push([
            'type' => 'signature',
            'value' => "{$pelabuhanAsal}, {$dateStr}",
        ]);
        $finalData->push([
            'type' => 'signature',
            'value' => 'PT. ALEXINDO YAKIN PRIMA',
        ]);
        for ($i = 0; $i < 5; $i++) {
            $finalData->push([
                'type' => 'signature',
                'value' => '',
            ]);
        }
        $finalData->push([
            'type' => 'signature',
            'value' => '( S U G E N G   R I A D I )',
        ]);

        return $finalData;
    }

    public function map($row): array
    {
        if ($row['type'] === 'signature') {
            $arr = array_fill(0, 33, '');
            $arr[22] = $row['value']; // Column W

            return $arr;
        }

        if ($row['type'] === 'total') {
            $arr = array_fill(0, 33, '');
            $arr[3] = 'Grand Total….'; // Column D
            $arr[5] = $row['qty_formula']; // Column F
            $arr[6] = 'Unit'; // Column G
            $arr[7] = $row['container_text']; // Column H
            $arr[14] = $row['weight_formula']; // Column O
            $arr[15] = $row['volume_formula']; // Column P

            return $arr;
        }

        $formattedDate = ! empty($row['tanggal'])
            ? (is_string($row['tanggal']) ? Carbon::parse($row['tanggal'])->format('d/m/Y') : $row['tanggal']->format('d/m/Y'))
            : '';

        $noKontainer = $row['no_kontainer'];
        $noSeal = $row['no_seal'];
        $size = $row['size'] ?: '20';

        $isCargo = $row['is_cargo'];
        $containerQty = $isCargo ? '' : 1;
        $containerUnit = $isCargo ? '' : 'Unit';
        $containerDesc = $isCargo ? '' : "Container {$size} feet stc :";

        $blNo = '';
        $shipperName = '';
        $shipperAddress = '';
        $shipperNpwp = '';
        $consigneeName = '';
        $consigneeAddress = '';
        $consigneeNpwp = '';
        $notifyName = '';
        $notifyAddress = '';
        $notifyNpwp = '';
        $deliveryAddress = '';
        $groupCount = '';
        $groupUnit = '';
        $groupDesc = '';

        if (! empty($row['show_group_fields'])) {
            $blNo = $row['group_number'];
            $shipperName = $row['pengirim'];
            $shipperAddress = $row['s_address'];

            $sLookup = $this->penerimaLookup[strtoupper(trim($row['pengirim']))] ?? null;
            $shipperNpwp = $sLookup['npwp'] ?? '-';

            $consigneeNpwp = $row['p_npwp'];
            $consigneeName = $row['penerima'];
            $consigneeAddress = $row['p_address'];

            $notifyName = $row['model']->notify_party ?: $row['penerima'];
            $nName = strtoupper(trim($notifyName));
            $nLookup = $this->penerimaLookup[$nName] ?? null;
            $notifyAddress = $row['model']->alamat_notify_party ?: ($nLookup['address'] ?? $row['p_address']);
            $notifyNpwp = $nLookup['npwp'] ?? $consigneeNpwp;

            $deliveryAddress = trim($consigneeName).'    '.trim($consigneeAddress);
            if (! empty($row['p_cp']) && $row['p_cp'] !== '-') {
                $deliveryAddress .= ' Telp.'.$row['p_cp'];
            }

            $groupCount = $row['group_count'];
            $groupUnit = 'Unit';
            $groupDesc = "Container {$size} feet / FCL  (Kantor)";
        }

        return [
            '', // A: Spacer
            $blNo, // B: B/L NO.
            '', // C: HS CODE
            $noKontainer, // D: MARK AND NUMBERS
            $noSeal, // E: SEAL NO.
            $containerQty, // F: Container Qty (1)
            $containerUnit, // G: Container Satuan ('Unit')
            $containerDesc, // H: Container Description
            $row['p_qty'], // I: Manifest Qty
            $row['p_satuan'], // J: Manifest Satuan
            $row['p_nama'], // K: Manifest Description of goods
            $row['p_qty'], // L: Perincian Qty
            $row['p_satuan'], // M: Perincian Satuan
            $row['p_nama'], // N: Perincian Description of goods
            $row['p_weight'], // O: Weight
            $row['p_meass'], // P: Meass
            $shipperName, // Q: SHIPPER
            $shipperAddress, // R: ADDRESS (Shipper Address)
            $shipperNpwp, // S: NPWP SHIPPER
            $consigneeName, // T: CONSIGNEE
            $consigneeAddress, // U: ADDRESS (Consignee Address)
            $consigneeNpwp, // V: NPWP CONSIGNEE
            $notifyName, // W: NOTIFY PARTY
            $notifyAddress, // X: ADDRESS (Notify Party Address)
            $notifyNpwp, // Y: NPWP NOTIFY PARTY
            $deliveryAddress, // Z: DELIVERY ADDRESS & CONTACT PERSON
            $row['ppftz'] === '-' ? 'AYP' : $row['ppftz'], // AA: DOCUMENT PPFTZ-03
            $row['term'] ?: '-', // AB: CONDITION (Term)
            $row['no_tt'] ?: '-', // AC: RECEIPT SIGNS (NUMBER)
            $formattedDate ?: '-', // AD: RECEIPT SIGNS (DATE)
            $groupCount, // AE: Qty Perhitungan
            $groupUnit, // AF: Unit Perhitungan
            $groupDesc, // AG: Container breakdown description
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(true);

        // Column widths
        $widths = [
            'A' => 2.57, 'B' => 5.71, 'C' => 10, 'D' => 16.71, 'E' => 10.71,
            'F' => 9.71, 'G' => 8.71, 'H' => 25.71, 'I' => 9.71, 'J' => 8.71,
            'K' => 80.71, 'L' => 9.71, 'M' => 8.71, 'N' => 80.71, 'O' => 16.14,
            'P' => 14.71, 'Q' => 26.71, 'R' => 26.71, 'S' => 20.57, 'T' => 26.71,
            'U' => 26.71, 'V' => 20.57, 'W' => 26.71, 'X' => 26.71, 'Y' => 20.57,
            'Z' => 26.71, 'AA' => 15.29, 'AB' => 15.14, 'AC' => 13.57, 'AD' => 18.71,
            'AE' => 14.71, 'AF' => 5.14, 'AG' => 33.14,
        ];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Row heights
        $rowHeights = [
            1 => 27.75, 2 => 15, 3 => 27.75, 4 => 18, 5 => 18,
            6 => 18, 7 => 15, 8 => 15, 9 => 25.5,
        ];
        foreach ($rowHeights as $r => $height) {
            $sheet->getRowDimension($r)->setRowHeight($height);
        }

        // Row 1 & 2 left headers
        $sheet->setCellValue('B1', 'PT. ALEXINDO YAKIN PRIMA');
        $sheet->setCellValue('B2', 'Perusahaan Pelayaran Nasional');

        $sheet->getStyle('B1')->getFont()->setName('Arial')->setSize(11)->setBold(true);
        $sheet->getStyle('B2')->getFont()->setName('Arial')->setSize(11)->setBold(false);

        // Row 3: Merged Title
        $sheet->setCellValue('B3', 'M A N I F E S T    O F    C A R G O');
        $sheet->mergeCells('B3:AG3');
        $sheet->getStyle('B3')->getFont()->setName('Arial')->setSize(14)->setBold(true);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Vessel, Voyage, Date info
        $firstManifest = $this->manifests->first();
        $vesselName = strtoupper($firstManifest->nama_kapal ?? '');
        $voyage = $firstManifest->no_voyage ?? '';

        Carbon::setLocale('id');
        $dateStr = $firstManifest && $firstManifest->tanggal_berangkat
            ? strtoupper(Carbon::parse($firstManifest->tanggal_berangkat)->translatedFormat('d F Y'))
            : strtoupper(Carbon::now()->translatedFormat('d F Y'));

        $sheet->setCellValue('Q4', 'NAMA KAPAL : '.$vesselName);
        $sheet->setCellValue('Q5', 'VOY : '.$voyage);
        $sheet->setCellValue('Q6', 'TANGGAL : '.$dateStr);

        $sheet->getStyle('Q4:Q6')->getFont()->setName('Arial')->setSize(11)->setBold(true);

        // Row 7 Headers
        $sheet->setCellValue('I7', 'MANIFEST');
        $sheet->mergeCells('I7:K7');
        $sheet->setCellValue('L7', 'PERINCIAN');
        $sheet->mergeCells('L7:N7');

        $sheet->getStyle('I7')->getFont()->setName('Arial')->setSize(11)->setBold(true);
        $sheet->getStyle('I7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF92D050');

        $sheet->getStyle('L7')->getFont()->setName('Arial')->setSize(11)->setBold(true);
        $sheet->getStyle('L7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF00B0F0');

        // Row 8 & 9 Headers
        $headers = [
            'B8' => 'B/L NO.',
            'C8' => 'HS CODE',
            'D8' => 'MARK AND NUMBERS',
            'E8' => 'SEAL NO.',
            'F8' => 'DESCRIPTION OF GOODS',
            'L8' => 'DESCRIPTION OF GOODS',
            'O8' => 'WEIGHT',
            'P8' => 'MEASS',
            'Q8' => 'SHIPPER',
            'R8' => 'ADDRESS',
            'S8' => "NO. IDENTITAS \n(NPWP  SHIPPER)",
            'T8' => 'CONSIGNEE',
            'U8' => 'ADDRESS',
            'V8' => "NO. IDENTITAS \n(NPWP  CONSIGNEE)",
            'W8' => "NOTIFY PARTY\n(CONSIGNEE)",
            'X8' => 'ADDRESS',
            'Y8' => "NO. IDENTITAS \n(NPWP  NOTIFY PARTY CONSIGNEE)",
            'Z8' => 'DELIVERY ADDRESS & CONTACT PERSON',
            'AA8' => 'DOCUMENT PPFTZ-03',
            'AB8' => 'CONDITION',
            'AC8' => 'RECEIPT SIGNS & ROAD LETTERS',
            'AE8' => "K E T E R A N G A N\nPERHITUNGAN M3 / KGS",
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->setCellValue('AC9', 'NUMBER');
        $sheet->setCellValue('AD9', 'DATE');

        $merges = [
            'B8:B9', 'C8:C9', 'D8:D9', 'E8:E9',
            'F8:K9', 'L8:N9', 'O8:O9', 'P8:P9',
            'Q8:Q9', 'R8:R9', 'S8:S9', 'T8:T9', 'U8:U9', 'V8:V9',
            'W8:W9', 'X8:X9', 'Y8:Y9', 'Z8:Z9',
            'AA8:AA9', 'AB8:AB9', 'AC8:AD8', 'AE8:AG9',
        ];
        foreach ($merges as $m) {
            $sheet->mergeCells($m);
        }

        $sheet->getStyle('B8:AG9')->getFont()->setName('Arial')->setSize(11)->setBold(true);
        $sheet->getStyle('B8:AG9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        foreach (['E8', 'AA8', 'AC8', 'AD8', 'AC9', 'AD9'] as $c) {
            $sheet->getStyle($c)->getFont()->setSize(10);
        }

        $fills = [
            'F8:K9' => 'FF92D050',
            'L8:N9' => 'FF00B0F0',
            'Q8:Q9' => 'FFFF0000',
            'T8:T9' => 'FF92D050',
            'W8:W9' => 'FFFFC000',
        ];
        foreach ($fills as $range => $color) {
            $sheet->getStyle($range)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($color);
        }

        // Borders for headers
        $sheet->getStyle('B7:AG9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Find ranges dynamically
        $highestRow = $sheet->getHighestRow();
        $totalRowIndex = 10;
        for ($r = 10; $r <= $highestRow; $r++) {
            if ($sheet->getCell("D{$r}")->getValue() === 'Grand Total….') {
                $totalRowIndex = $r;
                break;
            }
        }
        $lastDataRow = $totalRowIndex - 1;

        // Style data cells
        $sheet->getStyle("B10:AG{$lastDataRow}")->getFont()->setName('Arial')->setSize(10)->setBold(false);
        $sheet->getStyle("B10:AG{$lastDataRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle("B10:AG{$lastDataRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $centerCols = ['C', 'S', 'V', 'Y', 'AA', 'AB'];
        foreach ($centerCols as $col) {
            $sheet->getStyle("{$col}10:{$col}{$lastDataRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
        $sheet->getStyle("AA10:AA{$lastDataRow}")->getFont()->setBold(true);

        // Style Total Row
        $sheet->getStyle("B{$totalRowIndex}:AG{$totalRowIndex}")->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle("B{$totalRowIndex}:AG{$totalRowIndex}")->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle("B{$totalRowIndex}:AG{$totalRowIndex}")->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle("B{$totalRowIndex}:AG{$totalRowIndex}")->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);

        foreach (['O', 'P'] as $col) {
            $sheet->getStyle("{$col}{$totalRowIndex}")->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $sheet->getStyle("{$col}{$totalRowIndex}")->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        }

        $sheet->getStyle("B{$totalRowIndex}:AG{$totalRowIndex}")->getFont()->setName('Arial')->setSize(10);
        $sheet->getStyle("B{$totalRowIndex}:AG{$totalRowIndex}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle("D{$totalRowIndex}")->getFont()->setBold(true);
        $sheet->getStyle("O{$totalRowIndex}")->getFont()->setBold(true);
        $sheet->getStyle("P{$totalRowIndex}")->getFont()->setBold(true);

        // Style Signature Rows
        $sigStart = $totalRowIndex + 1;
        $sheet->getStyle("B{$sigStart}:AG{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);

        for ($r = $sigStart; $r <= $highestRow; $r++) {
            $sheet->getStyle("W{$r}")->getFont()->setName('Arial')->setSize(10)->setBold(true);
        }

        return [];
    }
}
