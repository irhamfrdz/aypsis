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

class ReportTandaTerimaJakartaExport implements FromCollection, WithCustomStartCell, WithMapping, WithStyles
{
    protected $startDate;

    protected $endDate;

    protected $status;

    protected $tujuan;

    protected $namaKapal;

    protected $noVoyage;

    protected $penerimaLookup = [];

    protected $termLookup = [];

    protected $vesselName = '-';

    protected $voyage = '-';

    public function __construct($startDate, $endDate, $status = 'semua', $tujuan = 'semua', $namaKapal = 'semua', $noVoyage = 'semua')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->tujuan = $tujuan;
        $this->namaKapal = $namaKapal;
        $this->noVoyage = $noVoyage;
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
                // If already exists but NPWP is empty, try to fill it
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

        // Look for common PPFTZ identifiers in PPBJ or other docs
        foreach ($docs as $doc) {
            if (is_string($doc) && (str_contains(strtoupper($doc), 'FTZ') || str_contains(strtoupper($doc), 'PPFTZ'))) {
                return $doc;
            }
        }

        // Fallback to first document if exists
        return $docs[0] ?? '-';
    }

    public function collection()
    {
        $data = collect();

        // Fetch all manifested tanda terima numbers for efficient lookup and vessel info
        $manifestedTTs = \Illuminate\Support\Facades\DB::table('manifests')
            ->whereNotNull('nomor_tanda_terima')
            ->where('nomor_tanda_terima', '!=', '')
            ->select('nomor_tanda_terima', 'nama_kapal', 'no_voyage', 'tanggal_berangkat')
            ->get()
            ->keyBy('nomor_tanda_terima');

        // 1. Tanda Terima (Standard)
        $ttStandard = TandaTerima::whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($item) use ($manifestedTTs) {
                // Extract items for perincian
                $items = [];
                $dimensi = ! empty($item->dimensi_details) ? $item->dimensi_details : $item->dimensi_items;

                if (! empty($dimensi)) {
                    $sumQty = collect($dimensi)->sum(function ($i) {
                        return data_get($i, 'jumlah') ?? data_get($i, 'qty') ?? 0;
                    });
                    $sumWeight = collect($dimensi)->sum(function ($i) {
                        return data_get($i, 'tonase') ?? 0;
                    });
                    $sumVolume = collect($dimensi)->sum(function ($i) {
                        return data_get($i, 'meter_kubik') ?? 0;
                    });
                    $names = collect($dimensi)->map(function ($i) use ($item) {
                        $n = data_get($i, 'nama_barang') ?: data_get($i, 'nama') ?: $item->nama_barang ?: $item->jenis_barang;
                        return is_array($n) ? implode(', ', $n) : $n;
                    })->filter()->unique()->implode(', ');
                    
                    $satuan = data_get($dimensi[0], 'satuan') ?? '';

                    $items = [[
                        'qty' => $sumQty,
                        'satuan' => $satuan,
                        'nama' => $names,
                        'weight' => $sumWeight,
                        'meass' => $sumVolume,
                    ]];
                } else {
                    $items = [[
                        'qty' => $item->jumlah ?? 0,
                        'satuan' => $item->satuan ?? '',
                        'nama' => ! empty($item->nama_barang) ? (is_array($item->nama_barang) ? implode(', ', $item->nama_barang) : $item->nama_barang) : $item->jenis_barang,
                        'weight' => $item->tonase ?? 0,
                        'meass' => $item->meter_kubik ?? 0,
                    ]];
                }

                $noTt = $item->no_surat_jalan ?? $item->surat_jalan?->no_surat_jalan ?? '-';
                $manifest = $manifestedTTs->get($noTt);

                return [
                    'source' => 'Standard',
                    'tanggal' => $item->tanggal,
                    'no_tt' => $noTt,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size,
                    'pengirim' => $item->pengirim,
                    'shipper_address_raw' => $item->alamat_pengirim,
                    'penerima' => $item->penerima,
                    'address_raw' => $item->alamat_penerima,
                    'cp_raw' => $item->pic_penerima,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->kegiatan,
                    'ppftz' => $this->getPpftzFromDocs($item->dokumen_ppbj),
                    'term' => $item->term,
                    'perincian_items' => $items,
                    'nama_kapal' => $manifest?->nama_kapal ?? null,
                    'no_voyage' => $manifest?->no_voyage ?? null,
                ];
            });
        $data = $data->concat($ttStandard);

        // 2. Tanda Terima Tanpa Surat Jalan
        $ttTSJ = TandaTerimaTanpaSuratJalan::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->with(['dimensiItems'])
            ->get()
            ->map(function ($item) use ($manifestedTTs) {
                $items = [];
                if ($item->dimensiItems->isNotEmpty()) {
                    $sumQty = $item->dimensiItems->sum('jumlah');
                    $sumWeight = $item->dimensiItems->sum('tonase');
                    $sumVolume = $item->dimensiItems->sum('meter_kubik');
                    $names = $item->dimensiItems->map(function ($i) use ($item) {
                        return $i->nama_barang ?: $i->nama ?: $item->nama_barang ?: $item->jenis_barang;
                    })->filter()->unique()->implode(', ');
                    
                    $satuan = $item->dimensiItems->first()->satuan ?? '';

                    $items = [[
                        'qty' => $sumQty,
                        'satuan' => $satuan,
                        'nama' => $names,
                        'weight' => $sumWeight,
                        'meass' => $sumVolume,
                    ]];
                } else {
                    $nama = $item->nama_barang ?: $item->jenis_barang;
                    $items = [[
                        'qty' => $item->jumlah_barang ?? 0,
                        'satuan' => $item->satuan_barang ?? '',
                        'nama' => $nama,
                        'weight' => $item->tonase ?? 0,
                        'meass' => $item->meter_kubik ?? 0,
                    ]];
                }

                $noTt = $item->no_tanda_terima;
                $manifest = $manifestedTTs->get($noTt);

                return [
                    'source' => 'Tanpa SJ',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $noTt,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->no_kontainer,
                    'no_seal' => $item->no_seal,
                    'size' => $item->size_kontainer,
                    'pengirim' => $item->pengirim,
                    'shipper_address_raw' => $item->alamat_pengirim,
                    'penerima' => $item->penerima,
                    'address_raw' => $item->alamat_penerima,
                    'cp_raw' => $item->pic_penerima ?: $item->pic,
                    'tujuan' => $item->tujuan_pengiriman,
                    'keterangan' => $item->aktifitas,
                    'ppftz' => $this->getPpftzFromDocs($item->dokumen_ppbj),
                    'term' => $this->termLookup[$item->term_id] ?? '-',
                    'perincian_items' => $items,
                    'nama_kapal' => $manifest?->nama_kapal ?? null,
                    'no_voyage' => $manifest?->no_voyage ?? null,
                ];
            });
        $data = $data->concat($ttTSJ);

        // 3. Tanda Terima LCL
        $ttLCL = TandaTerimaLcl::whereBetween('tanggal_tanda_terima', [$this->startDate, $this->endDate])
            ->with(['tujuanKirim', 'kontainerPivot', 'items'])
            ->get()
            ->map(function ($item) use ($manifestedTTs) {
                $items = [];
                if ($item->items->isNotEmpty()) {
                    $sumQty = $item->items->sum('jumlah');
                    $sumWeight = $item->items->sum('tonase');
                    $sumVolume = $item->items->sum('meter_kubik');
                    $names = $item->items->map(function ($i) use ($item) {
                        return $i->nama_barang ?: $i->nama ?: $item->nama_barang ?: $item->kegiatan ?: $item->jenis_barang;
                    })->filter()->unique()->implode(', ');
                    
                    $satuan = $item->items->first()->satuan ?? '';

                    $items = [[
                        'qty' => $sumQty,
                        'satuan' => $satuan,
                        'nama' => $names,
                        'weight' => $sumWeight,
                        'meass' => $sumVolume,
                    ]];
                } else {
                    $items = [[
                        'qty' => 0,
                        'satuan' => '',
                        'nama' => $item->nama_barang ?: $item->kegiatan ?: $item->jenis_barang,
                        'weight' => 0,
                        'meass' => 0,
                    ]];
                }

                $noTt = $item->nomor_tanda_terima;
                $manifest = $manifestedTTs->get($noTt);

                return [
                    'source' => 'LCL',
                    'tanggal' => $item->tanggal_tanda_terima,
                    'no_tt' => $noTt,
                    'no_sj_pabrik' => $item->surat_jalan_pabrik,
                    'no_kontainer' => $item->nomor_kontainer,
                    'no_seal' => $item->nomor_seal,
                    'size' => $item->kontainerPivot->first()->size_kontainer ?? '-',
                    'pengirim' => $item->nama_pengirim,
                    'shipper_address_raw' => $item->alamat_pengirim,
                    'penerima' => $item->nama_penerima,
                    'address_raw' => $item->alamat_penerima,
                    'cp_raw' => $item->pic_penerima,
                    'tujuan' => $item->tujuanKirim?->nama_tujuan ?? '-',
                    'keterangan' => $item->kegiatan,
                    'ppftz' => $this->getPpftzFromDocs($item->dokumen_ppbj),
                    'term' => $this->termLookup[$item->term_id] ?? '-',
                    'perincian_items' => $items,
                    'nama_kapal' => $manifest?->nama_kapal ?? null,
                    'no_voyage' => $manifest?->no_voyage ?? null,
                ];
            });
        $data = $data->concat($ttLCL);

        // Enhance with lookup data
        $enhancedData = $data->map(function ($item) use ($manifestedTTs) {
            $pName = strtoupper(trim($item['penerima']));
            $pLookup = $this->penerimaLookup[$pName] ?? null;

            $sName = strtoupper(trim($item['pengirim']));
            $sLookup = $this->penerimaLookup[$sName] ?? null;

            // Prioritize address and CP from the record itself, fallback to lookup
            $item['p_address'] = $item['address_raw'] ?: ($pLookup['address'] ?? '-');
            $item['p_cp'] = $item['cp_raw'] ?: ($pLookup['cp'] ?? '-');
            $item['p_npwp'] = $pLookup['npwp'] ?? '-';

            $item['s_address'] = $item['shipper_address_raw'] ?: ($sLookup['address'] ?? '-');

            // Add manifest status
            $item['naik_kapal'] = $manifestedTTs->has($item['no_tt']);

            return $item;
        });

        // Filter based on status
        if ($this->status === 'belum') {
            $enhancedData = $enhancedData->where('naik_kapal', false);
        } elseif ($this->status === 'sudah') {
            $enhancedData = $enhancedData->where('naik_kapal', true);
        }

        // Filter based on tujuan (destination)
        if ($this->tujuan === 'batam') {
            $enhancedData = $enhancedData->filter(function ($item) {
                $tuj = strtolower($item['tujuan'] ?? '');

                return str_contains($tuj, 'batam');
            });
        } elseif ($this->tujuan === 'tanjungpinang') {
            $enhancedData = $enhancedData->filter(function ($item) {
                $tuj = strtolower($item['tujuan'] ?? '');

                return str_contains($tuj, 'tanjung pinang') || str_contains($tuj, 'tanjungpinang');
            });
        }

        // Filter based on kapal
        if ($this->namaKapal !== 'semua') {
            $enhancedData = $enhancedData->filter(function ($item) {
                return strtolower($item['nama_kapal'] ?? '') === strtolower($this->namaKapal);
            });
        }

        // Filter based on voyage
        if ($this->noVoyage !== 'semua') {
            $enhancedData = $enhancedData->filter(function ($item) {
                return ($item['no_voyage'] ?? '') === $this->noVoyage;
            });
        }

        // Capture first vessel name and voyage for display in header
        foreach ($enhancedData as $item) {
            if (! empty($item['nama_kapal']) && $this->vesselName === '-') {
                $this->vesselName = $item['nama_kapal'];
            }
            if (! empty($item['no_voyage']) && $this->voyage === '-') {
                $this->voyage = $item['no_voyage'];
            }
        }

        // Sort data first to enable grouping
        $sortedData = $enhancedData->sortBy([
            [function ($item) {
                $hasContainer = ! empty($item['no_kontainer']) && $item['no_kontainer'] != '-';
                $hasSeal = ! empty($item['no_seal']) && $item['no_seal'] != '-';

                return ($hasContainer && $hasSeal) ? 0 : 1;
            }, 'asc'],
            ['source', 'asc'],
            ['no_kontainer', 'asc'],
            ['no_seal', 'asc'],
            ['tanggal', 'desc'],
        ]);

        // Group by container & seal info
        $grouped = $sortedData->groupBy(function ($item, $key) {
            $hasContainer = ! empty($item['no_kontainer']) && $item['no_kontainer'] != '-';
            if ($hasContainer) {
                $seal = $item['no_seal'] ?: 'none';
                return $item['no_kontainer'] . '|' . $seal;
            }
            return 'empty_' . $key;
        });

        $finalData = collect();
        $groupCounter = 1;

        foreach ($grouped as $key => $items) {
            $firstItem = $items->first();
            $hasInfo = ! empty($firstItem['no_kontainer']) && $firstItem['no_kontainer'] != '-';

            // Determine if LCL or Cargo
            $isLcl = ($firstItem['source'] === 'LCL') || (stripos($firstItem['size'] ?? '', 'LCL') !== false);
            $isCargo = (stripos($firstItem['size'] ?? '', 'Cargo') !== false);

            $groupNumber = '';
            // If the group has container info and is not cargo, assign it a group number
            if ($hasInfo && ! $isCargo) {
                $groupNumber = sprintf('%02d', $groupCounter);
                $groupCounter++;
            }

            $itemsCount = count($items);
            foreach ($items as $idx => $item) {
                $perincian = $item['perincian_items'] ?? [];
                if (empty($perincian)) {
                    $perincian = [['qty' => '', 'satuan' => '', 'nama' => '', 'weight' => '', 'meass' => '']];
                }

                // Determine if LCL for this specific item
                $itemIsLcl = ($item['source'] === 'LCL') || (stripos($item['size'] ?? '', 'LCL') !== false);

                foreach ($perincian as $pIdx => $pItem) {
                    $row = $item;
                    $row['type'] = 'item';
                    $row['is_lcl'] = $itemIsLcl;
                    $row['is_cargo'] = (stripos($item['size'] ?? '', 'Cargo') !== false);

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
        $sizes = $sortedData->pluck('size')->filter()->unique();
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
        $pelabuhanAsal = 'JAKARTA';
        Carbon::setLocale('id');
        $dateStr = strtoupper(Carbon::now()->translatedFormat('d F Y'));

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
            $shipperName = $row['pengirim'];
            $shipperAddress = $row['s_address'];

            $sLookup = $this->penerimaLookup[strtoupper(trim($row['pengirim']))] ?? null;
            $shipperNpwp = $sLookup['npwp'] ?? '-';

            $consigneeNpwp = $row['p_npwp'];
            $consigneeName = $row['penerima'];
            $consigneeAddress = $row['p_address'];

            $notifyName = $row['penerima'];
            $notifyAddress = $row['p_address'];
            $notifyNpwp = $consigneeNpwp;

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
            '', // B: B/L NO. (Left empty per user request)
            '', // C: HS CODE (Left empty per user request)
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
        $vesselName = strtoupper($this->vesselName ?? '-');
        $voyage = $this->voyage ?? '-';

        Carbon::setLocale('id');
        $startDateStr = strtoupper(Carbon::parse($this->startDate)->translatedFormat('d F Y'));
        $endDateStr = strtoupper(Carbon::parse($this->endDate)->translatedFormat('d F Y'));
        $dateStr = "{$startDateStr} - {$endDateStr}";

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
