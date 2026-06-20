<?php

namespace App\Exports;

use App\Models\Prospek;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProspekManifestExport implements FromCollection, WithCustomStartCell, WithMapping, WithStyles
{
    protected $filters;

    protected $prospekIds;

    protected $startDate;

    protected $endDate;

    protected $penerimaLookup = [];

    protected $termLookup = [];

    protected $vesselName = '-';

    protected $voyage = '-';

    public function __construct(array $filters = [], array $prospekIds = [])
    {
        $this->filters = $filters;
        $this->prospekIds = $prospekIds;
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
        // If prospekIds provided, export only those
        if (! empty($this->prospekIds)) {
            $query = Prospek::with(['suratJalan', 'tandaTerima'])->whereIn('id', $this->prospekIds);
        } else {
            $query = Prospek::with(['suratJalan', 'tandaTerima'])->orderBy('created_at', 'desc');

            if (! empty($this->filters['status'])) {
                if ($this->filters['status'] == 'sudah_muat_no_voyage') {
                    $query->where('status', 'sudah_muat')
                        ->where(function ($q) {
                            $q->whereNull('no_voyage')->orWhere('no_voyage', '');
                        });
                } else {
                    $query->where('status', $this->filters['status']);
                }
            }
            if (! empty($this->filters['tipe'])) {
                $query->where('tipe', $this->filters['tipe']);
            }
            if (! empty($this->filters['ukuran'])) {
                $query->where('ukuran', $this->filters['ukuran']);
            }
            if (! empty($this->filters['tujuan'])) {
                $query->where('tujuan_pengiriman', 'like', '%'.$this->filters['tujuan'].'%');
            }
            if (! empty($this->filters['tanggal_dari']) && ! empty($this->filters['tanggal_sampai'])) {
                $query->whereBetween('tanggal', [$this->filters['tanggal_dari'], $this->filters['tanggal_sampai']]);
            }
            if (! empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('nama_supir', 'like', "%{$search}%")
                        ->orWhere('barang', 'like', "%{$search}%")
                        ->orWhere('pt_pengirim', 'like', "%{$search}%")
                        ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                        ->orWhere('no_seal', 'like', "%{$search}%")
                        ->orWhere('tujuan_pengiriman', 'like', "%{$search}%")
                        ->orWhere('nama_kapal', 'like', "%{$search}%")
                        ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('no_voyage', 'like', "%{$search}%");
                });
            }

            if (! empty($this->filters['show_duplicates']) && $this->filters['show_duplicates'] == '1') {
                $duplicateNos = Prospek::select('no_surat_jalan')
                    ->whereNotNull('no_surat_jalan')
                    ->where('no_surat_jalan', '!=', '')
                    ->groupBy('no_surat_jalan')
                    ->havingRaw('COUNT(no_surat_jalan) > 1')
                    ->pluck('no_surat_jalan');

                $query->whereIn('no_surat_jalan', $duplicateNos);
            }
        }

        $prospeks = $query->get();

        // Determine start and end dates for header
        $dates = $prospeks->pluck('tanggal')->filter();
        $this->startDate = $dates->min() ? (is_string($dates->min()) ? $dates->min() : $dates->min()->format('Y-m-d')) : Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $dates->max() ? (is_string($dates->max()) ? $dates->max() : $dates->max()->format('Y-m-d')) : Carbon::now()->format('Y-m-d');

        // Pre-fetch related TandaTerima items
        $noSjs = $prospeks->pluck('no_surat_jalan')->filter()->unique();
        $tanpaSjs = TandaTerimaTanpaSuratJalan::whereIn('no_tanda_terima', $noSjs)->get()->keyBy('no_tanda_terima');
        $lcls = TandaTerimaLcl::whereIn('nomor_tanda_terima', $noSjs)->with(['tujuanKirim', 'kontainerPivot', 'items'])->get()->keyBy('nomor_tanda_terima');

        $data = $prospeks->map(function ($p) use ($tanpaSjs, $lcls) {
            $tt = $p->tandaTerima;
            $tsj = $tanpaSjs->get($p->no_surat_jalan);
            $lcl = $lcls->get($p->no_surat_jalan);

            $source = 'Standard';
            if ($lcl || strtoupper($p->tipe) === 'LCL') {
                $source = 'LCL';
            } elseif ($tsj) {
                $source = 'Tanpa SJ';
            }

            // Determine perincian items
            $items = [];
            if ($source === 'LCL') {
                if ($lcl && $lcl->items->isNotEmpty()) {
                    $sumQty = $lcl->items->sum('jumlah');
                    $sumWeight = $lcl->items->sum('tonase');
                    $sumVolume = $lcl->items->sum('meter_kubik');
                    $names = $lcl->items->map(function ($i) use ($lcl) {
                        return $i->nama_barang ?: $i->nama ?: $lcl->nama_barang ?: $lcl->kegiatan ?: $lcl->jenis_barang;
                    })->filter()->unique()->implode(', ');

                    $satuan = $lcl->items->first()->satuan ?? '';

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
                        'nama' => $p->barang,
                        'weight' => $p->total_ton ?? 0,
                        'meass' => $p->total_volume ?? 0,
                    ]];
                }
            } elseif ($source === 'Tanpa SJ') {
                if ($tsj && $tsj->dimensiItems->isNotEmpty()) {
                    $sumQty = $tsj->dimensiItems->sum('jumlah');
                    $sumWeight = $tsj->dimensiItems->sum('tonase');
                    $sumVolume = $tsj->dimensiItems->sum('meter_kubik');
                    $names = $tsj->dimensiItems->map(function ($i) use ($tsj) {
                        return $i->nama_barang ?: $i->nama ?: $tsj->nama_barang ?: $tsj->jenis_barang;
                    })->filter()->unique()->implode(', ');

                    $satuan = $tsj->dimensiItems->first()->satuan ?? '';

                    $items = [[
                        'qty' => $sumQty,
                        'satuan' => $satuan,
                        'nama' => $names,
                        'weight' => $sumWeight,
                        'meass' => $sumVolume,
                    ]];
                } else {
                    $items = [[
                        'qty' => $p->kuantitas ?? 0,
                        'satuan' => $tsj?->satuan_barang ?? '',
                        'nama' => $p->barang,
                        'weight' => $p->total_ton ?? 0,
                        'meass' => $p->total_volume ?? 0,
                    ]];
                }
            } else { // Standard
                $dimensi = ! empty($tt?->dimensi_details) ? $tt->dimensi_details : ($tt?->dimensi_items ?? []);
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
                    $names = collect($dimensi)->map(function ($i) use ($tt) {
                        $n = data_get($i, 'nama_barang') ?: data_get($i, 'nama') ?: $tt->nama_barang ?: $tt->jenis_barang;

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
                        'qty' => $p->kuantitas ?? $tt?->jumlah ?? 0,
                        'satuan' => $tt?->satuan ?? '',
                        'nama' => $p->barang,
                        'weight' => $p->total_ton ?? 0,
                        'meass' => $p->total_volume ?? 0,
                    ]];
                }
            }

            $noSjPabrik = $tt?->surat_jalan_pabrik ?? $tsj?->surat_jalan_pabrik ?? $lcl?->surat_jalan_pabrik ?? '-';
            $ppftz = $this->getPpftzFromDocs($tt?->dokumen_ppbj ?? $tsj?->dokumen_ppbj ?? $lcl?->dokumen_ppbj);
            $term = $tt?->term ?? ($tsj?->term_id ? ($this->termLookup[$tsj->term_id] ?? '-') : ($lcl?->term_id ? ($this->termLookup[$lcl->term_id] ?? '-') : '-'));

            return [
                'source' => $source,
                'tanggal' => $p->tanggal,
                'no_tt' => $p->no_surat_jalan,
                'no_sj_pabrik' => $noSjPabrik,
                'no_kontainer' => $p->nomor_kontainer,
                'no_seal' => $p->no_seal,
                'size' => $p->ukuran,
                'pengirim' => $p->pt_pengirim,
                'shipper_address_raw' => $tt?->alamat_pengirim ?? $tsj?->alamat_pengirim ?? $lcl?->alamat_pengirim ?? '',
                'penerima' => $p->penerima,
                'address_raw' => $tt?->alamat_penerima ?? $tsj?->alamat_penerima ?? $lcl?->alamat_penerima ?? '',
                'cp_raw' => $tt?->pic_penerima ?? $tsj?->pic_penerima ?? $tsj?->pic ?? $lcl?->pic_penerima ?? '',
                'tujuan' => $p->tujuan_pengiriman,
                'keterangan' => $p->keterangan,
                'ppftz' => $ppftz,
                'term' => $term,
                'perincian_items' => $items,
                'nama_kapal' => $p->nama_kapal,
                'no_voyage' => $p->no_voyage,
                'bl_no' => $p->suratJalan?->no_surat_jalan ?? null,
                'tipe_kontainer' => $p->tipe,
                'naik_kapal' => $p->status === 'sudah_muat',
            ];
        });

        // Enhance with lookup data
        $enhancedData = $data->map(function ($item) {
            $pName = strtoupper(trim($item['penerima']));
            $pLookup = $this->penerimaLookup[$pName] ?? null;

            $sName = strtoupper(trim($item['pengirim']));
            $sLookup = $this->penerimaLookup[$sName] ?? null;

            // Prioritize address and CP from the record itself, fallback to lookup
            $item['p_address'] = $item['address_raw'] ?: ($pLookup['address'] ?? '-');
            $item['p_cp'] = $item['cp_raw'] ?: ($pLookup['cp'] ?? '-');
            $item['p_npwp'] = $pLookup['npwp'] ?? '-';

            $item['s_address'] = $item['shipper_address_raw'] ?: ($sLookup['address'] ?? '-');

            // Determine LCL
            $item['is_lcl'] = ($item['source'] === 'LCL') ||
                              (stripos($item['size'] ?? '', 'LCL') !== false) ||
                              (! empty($item['tipe_kontainer']) && stripos($item['tipe_kontainer'], 'LCL') !== false);
            // Determine Cargo
            $item['is_cargo'] = (stripos($item['size'] ?? '', 'Cargo') !== false) ||
                                (! empty($item['tipe_kontainer']) && stripos($item['tipe_kontainer'], 'Cargo') !== false);

            return $item;
        });

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

                return $item['no_kontainer'].'|'.$seal;
            }

            if ($item['source'] === 'Standard') {
                $pengirim = trim($item['pengirim'] ?? '');
                $penerima = trim($item['penerima'] ?? '');

                return 'empty_standard_'.$pengirim.'|'.$penerima;
            }

            return 'empty_'.$key;
        });

        // Sort grouped collection: LCL groups first, then FCL, then Cargo/empty last
        $grouped = $grouped->sort(function ($a, $b) {
            $isLclA = $a->contains('is_lcl', true);
            $isLclB = $b->contains('is_lcl', true);

            $isCargoA = $a->contains('is_cargo', true) || empty($a->first()['no_kontainer']) || $a->first()['no_kontainer'] === '-';
            $isCargoB = $b->contains('is_cargo', true) || empty($b->first()['no_kontainer']) || $b->first()['no_kontainer'] === '-';

            if ($isLclA && ! $isLclB) {
                return -1; // a (LCL) comes first
            }
            if (! $isLclA && $isLclB) {
                return 1; // b (LCL) comes first
            }

            if ($isCargoA && ! $isCargoB) {
                return 1; // a (Cargo/empty) comes last
            }
            if (! $isCargoA && $isCargoB) {
                return -1; // b (Cargo/empty) comes last
            }

            // If both are LCL, sort by container number
            if ($isLclA && $isLclB) {
                return strnatcasecmp($a->first()['no_kontainer'] ?? '', $b->first()['no_kontainer'] ?? '');
            }

            // For FCL and Cargo, sort by pengirim and penerima so same shipper/consignee are adjacent
            $pengirimA = trim($a->first()['pengirim'] ?? '');
            $penerimaA = trim($a->first()['penerima'] ?? '');
            $pengirimB = trim($b->first()['pengirim'] ?? '');
            $penerimaB = trim($b->first()['penerima'] ?? '');

            $compPengirim = strcasecmp($pengirimA, $pengirimB);
            if ($compPengirim !== 0) {
                return $compPengirim;
            }
            $compPenerima = strcasecmp($penerimaA, $penerimaB);
            if ($compPenerima !== 0) {
                return $compPenerima;
            }

            return strnatcasecmp($a->first()['no_kontainer'] ?? '', $b->first()['no_kontainer'] ?? '');
        });

        $finalData = collect();
        $groupCounter = 1;

        foreach ($grouped as $key => $items) {
            $firstItem = $items->first();
            $hasInfo = ! empty($firstItem['no_kontainer']) && $firstItem['no_kontainer'] != '-';

            // Check if this group represents an LCL container
            $isLclGroup = $items->contains('is_lcl', true);

            $groupNumber = '';
            if ($hasInfo && ! $firstItem['is_cargo']) {
                $groupNumber = sprintf('%02d', $groupCounter);
                $groupCounter++;
            }

            if ($isLclGroup && $hasInfo) {
                // Calculate LCL total weight and volume
                $totalWeight = $items->sum(function ($item) {
                    return collect($item['perincian_items'])->sum('weight');
                });
                $totalVolume = $items->sum(function ($item) {
                    return collect($item['perincian_items'])->sum('meass');
                });

                // 1. Output LCL Container Header Row
                $headerRow = $firstItem;
                $headerRow['type'] = 'lcl_container_header';
                $headerRow['group_number'] = $groupNumber;
                $headerRow['p_qty'] = '';
                $headerRow['p_satuan'] = '';
                $headerRow['p_nama'] = 'General cargo';
                $headerRow['p_weight'] = $totalWeight;
                $headerRow['p_meass'] = $totalVolume;
                $headerRow['pengirim'] = 'Pt. Alexindo Yakinprima - Jakarta';
                $headerRow['s_address'] = '-';

                $finalData->push($headerRow);

                // Group LCL items by pengirim & penerima for Standard source items
                $lclItems = $items->groupBy(function ($item, $k) {
                    if ($item['source'] === 'Standard') {
                        return 'standard_'.trim($item['pengirim'] ?? '').'|'.trim($item['penerima'] ?? '');
                    }

                    return 'other_'.$k;
                })->collapse();

                // 2. Output LCL Manifest Rows
                foreach ($lclItems as $idx => $item) {
                    $perincian = $item['perincian_items'] ?? [];
                    if (empty($perincian)) {
                        $perincian = [['qty' => '', 'satuan' => '', 'nama' => '', 'weight' => '', 'meass' => '']];
                    }

                    // Condense perincian details into 1 row
                    if (count($perincian) > 1) {
                        $pItem = [
                            'qty' => implode("\n", array_column($perincian, 'qty')),
                            'satuan' => implode("\n", array_column($perincian, 'satuan')),
                            'nama' => implode("\n", array_column($perincian, 'nama')),
                            'weight' => implode("\n", array_column($perincian, 'weight')),
                            'meass' => implode("\n", array_column($perincian, 'meass')),
                        ];
                        $perincian = [$pItem];
                    }

                    foreach ($perincian as $pItem) {
                        $row = $item;
                        $row['type'] = 'lcl_manifest_row';
                        $row['group_number'] = $groupNumber.chr(65 + $idx); // e.g., 01A, 01B...

                        $row['p_qty'] = $pItem['qty'];
                        $row['p_satuan'] = $pItem['satuan'];
                        $row['p_nama'] = $pItem['nama'];
                        $row['p_weight'] = $pItem['weight'];
                        $row['p_meass'] = $pItem['meass'];

                        $finalData->push($row);
                    }
                }
            } else {
                // Group normal items by pengirim & penerima for Standard source items
                $normalItems = $items->groupBy(function ($item, $k) {
                    if ($item['source'] === 'Standard') {
                        return 'standard_'.trim($item['pengirim'] ?? '').'|'.trim($item['penerima'] ?? '');
                    }

                    return 'other_'.$k;
                })->collapse();

                // FCL or Cargo (Normal)
                $itemsCount = count($normalItems);
                foreach ($normalItems as $idx => $item) {
                    $perincian = $item['perincian_items'] ?? [];
                    if (empty($perincian)) {
                        $perincian = [['qty' => '', 'satuan' => '', 'nama' => '', 'weight' => '', 'meass' => '']];
                    }

                    // Condense perincian details into 1 row
                    if (count($perincian) > 1) {
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

        $isLclManifestRow = ($row['type'] === 'lcl_manifest_row');
        $noKontainer = $isLclManifestRow ? '' : $row['no_kontainer'];
        $noSeal = $isLclManifestRow ? '' : $row['no_seal'];
        $size = $row['size'] ?: '20';

        $isCargo = $row['is_cargo'];
        $containerQty = ($isCargo || $isLclManifestRow) ? '' : 1;
        $containerUnit = ($isCargo || $isLclManifestRow) ? '' : 'Unit';
        $containerDesc = ($isCargo || $isLclManifestRow) ? '' : "Container {$size} feet stc :";

        $blNo = '';
        $hsCode = '';
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

        if ($row['type'] === 'lcl_container_header') {
            $blNo = $row['group_number'];
            $shipperName = $row['pengirim'];
            $shipperAddress = $row['s_address'];
            $shipperNpwp = '-';
            $consigneeName = '';
            $consigneeAddress = '';
            $consigneeNpwp = '-';
            $notifyName = '';
            $notifyAddress = '';
            $notifyNpwp = '-';
            $deliveryAddress = '';
            $groupCount = 1;
            $groupUnit = 'Unit';
            $groupDesc = "Container {$size} feet / LCL  (Kantor)";
        } elseif ($row['type'] === 'lcl_manifest_row') {
            $blNo = $row['bl_no'] ?: $row['group_number'];
            $hsCode = '';

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
        } else {
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

            if (! empty($row['show_group_fields'])) {
                $blNo = $row['bl_no'];
                $groupCount = $row['group_count'];
                $groupUnit = 'Unit';
                $groupDesc = "Container {$size} feet / FCL  (Kantor)";
            }
        }

        $formattedDate = ! empty($row['tanggal'])
            ? (is_string($row['tanggal']) ? Carbon::parse($row['tanggal'])->format('d/m/Y') : $row['tanggal']->format('d/m/Y'))
            : '';

        $blNo = ''; // Empty per request

        return [
            '', // A: Spacer
            $blNo, // B: B/L NO.
            $hsCode, // C: HS CODE
            $noKontainer, // D: MARK AND NUMBERS
            $noSeal, // E: SEAL NO.
            $containerQty, // F: Container Qty
            $containerUnit, // G: Container Satuan
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
