<?php

namespace App\Http\Controllers;

use App\Models\LangsirBatam;
use App\Models\MasterPricelistLoloBatam;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\TagihanLoloBatam;
use App\Models\TagihanLoloBatamItem;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TagihanLoloBatamController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'kontainer');
        $search = $request->get('search');
        $statusPembayaran = $request->get('status_pembayaran');
        $statusTagihan = $request->get('status_tagihan'); // 'belum', 'sudah', or ''
        $sumber = $request->get('sumber', 'all'); // 'all', 'bongkaran', 'langsir'
        $vendor = $request->get('vendor');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // 1. INVOICE QUERY & STATS (Faktur Tab)
        $invoiceQuery = TagihanLoloBatam::with(['createdBy', 'updatedBy', 'items', 'operatorKaryawan'])->latest('tanggal_tagihan');

        if ($search && $activeTab === 'faktur') {
            $invoiceQuery->search($search);
        }

        if ($statusPembayaran) {
            $invoiceQuery->where('status_pembayaran', $statusPembayaran);
        }

        if ($vendor) {
            $invoiceQuery->where('vendor', 'like', '%'.$vendor.'%');
        }

        if ($startDate && $activeTab === 'faktur') {
            $invoiceQuery->whereDate('tanggal_tagihan', '>=', $startDate);
        }

        if ($endDate && $activeTab === 'faktur') {
            $invoiceQuery->whereDate('tanggal_tagihan', '<=', $endDate);
        }

        $totalTagihanCount = TagihanLoloBatam::count();
        $totalNominal = TagihanLoloBatam::sum('total_tagihan');
        $totalLunas = TagihanLoloBatam::where('status_pembayaran', 'Lunas')->sum('total_tagihan');
        $totalBelumLunas = TagihanLoloBatam::where('status_pembayaran', 'Belum Lunas')->sum('total_tagihan');
        $countBelumLunas = TagihanLoloBatam::where('status_pembayaran', 'Belum Lunas')->count();

        $tagihans = $invoiceQuery->paginate(20, ['*'], 'faktur_page')->withQueryString();

        // 2. LOLO CONTAINERS QUERY (Kontainer LOLO Tab)
        $pricelists = MasterPricelistLoloBatam::aktif()->get();

        $matchPricelist = function ($containerSize) use ($pricelists) {
            if (empty($containerSize)) {
                return $pricelists->firstWhere('size', '20') ?? $pricelists->first();
            }

            // 1. Exact match (case-insensitive & trimmed)
            $exact = $pricelists->first(function ($p) use ($containerSize) {
                return strcasecmp(trim($p->size), trim($containerSize)) === 0;
            });
            if ($exact) {
                return $exact;
            }

            // 2. Numeric digits match (e.g. '20FT', '20 FT', '20 feet' matches '20' or '20FT')
            $numSize = preg_replace('/[^0-9]/', '', (string) $containerSize);
            if ($numSize !== '') {
                $numMatch = $pricelists->first(function ($p) use ($numSize) {
                    $pNum = preg_replace('/[^0-9]/', '', (string) $p->size);

                    return $pNum !== '' && $pNum === $numSize;
                });
                if ($numMatch) {
                    return $numMatch;
                }
            }

            // 3. Substring match
            return $pricelists->first(function ($p) use ($containerSize) {
                return stripos($containerSize, (string) $p->size) !== false || stripos((string) $p->size, (string) $containerSize) !== false;
            });
        };

        $billedBongkarans = TagihanLoloBatamItem::with(['tagihanLoloBatam.operatorKaryawan'])
            ->whereNotNull('surat_jalan_bongkaran_id')
            ->get()
            ->keyBy('surat_jalan_bongkaran_id');

        $billedLangsirs = TagihanLoloBatamItem::with(['tagihanLoloBatam.operatorKaryawan'])
            ->whereNotNull('langsir_batam_id')
            ->get()
            ->keyBy('langsir_batam_id');

        $containers = collect();

        // Bongkaran Batam containers with LOLO = 1
        if ($sumber === 'all' || $sumber === 'bongkaran') {
            $bongkaranQuery = SuratJalanBongkaranBatam::where('menggunakan_lolo', 1);

            if ($search && $activeTab !== 'faktur') {
                $bongkaranQuery->where(function ($q) use ($search) {
                    $q->where('no_kontainer', 'like', '%'.$search.'%')
                        ->orWhere('nomor_surat_jalan', 'like', '%'.$search.'%')
                        ->orWhere('nama_kapal', 'like', '%'.$search.'%')
                        ->orWhere('no_voyage', 'like', '%'.$search.'%')
                        ->orWhere('supir', 'like', '%'.$search.'%');
                });
            }

            if ($startDate && $activeTab !== 'faktur') {
                $bongkaranQuery->whereDate('tanggal_surat_jalan', '>=', $startDate);
            }

            if ($endDate && $activeTab !== 'faktur') {
                $bongkaranQuery->whereDate('tanggal_surat_jalan', '<=', $endDate);
            }

            $bongkarans = $bongkaranQuery->latest('tanggal_surat_jalan')->get();

            foreach ($bongkarans as $b) {
                $isBilled = isset($billedBongkarans[$b->id]);
                $tagihanItem = $isBilled ? $billedBongkarans[$b->id] : null;

                if ($statusTagihan === 'belum' && $isBilled) {
                    continue;
                }
                if ($statusTagihan === 'sudah' && ! $isBilled) {
                    continue;
                }

                $operatorName = null;
                $tipeOperator = null;
                if ($tagihanItem && $tagihanItem->tagihanLoloBatam) {
                    $tlb = $tagihanItem->tagihanLoloBatam;
                    $tipeOperator = $tlb->tipe_operator;
                    $operatorName = $tlb->operator ?: ($tlb->operatorKaryawan->nama_lengkap ?? ($tipeOperator === 'VENDOR' ? $tlb->vendor : null));
                }

                $size = $b->size ?? '20';
                $matchedPl = $matchPricelist($size);
                $totalTarif = $isBilled && $tagihanItem ? ($tagihanItem->total ?? $tagihanItem->tarif) : ($matchedPl ? (float) $matchedPl->tarif : 0);
                $displaySize = (preg_replace('/[^0-9]/', '', (string) $size) ?: $size)."'";

                $containers->push((object) [
                    'sumber' => 'bongkaran',
                    'id' => $b->id,
                    'nomor_surat_jalan' => $b->nomor_surat_jalan,
                    'tanggal' => $b->tanggal_surat_jalan,
                    'no_kontainer' => $b->no_kontainer,
                    'size' => $size,
                    'display_size' => $displaySize,
                    'total_tarif' => $totalTarif,
                    'formatted_total_tarif' => $totalTarif > 0 ? 'Rp '.number_format($totalTarif, 0, ',', '.') : '-',
                    'tipe_kontainer' => $b->tipe_kontainer ?? ($b->f_e ? strtoupper($b->f_e) : 'FULL'),
                    'kapal' => $b->nama_kapal ?? '-',
                    'voyage' => $b->no_voyage ?? '-',
                    'supir' => $b->supir ?? '-',
                    'no_plat' => $b->no_plat ?? '-',
                    'lokasi' => $b->lokasi ?? 'Batam',
                    'is_billed' => $isBilled,
                    'tipe_operator' => $tipeOperator,
                    'operator' => $operatorName,
                    'tagihan_id' => $tagihanItem && $tagihanItem->tagihanLoloBatam ? $tagihanItem->tagihanLoloBatam->id : null,
                    'nomor_tagihan' => $tagihanItem && $tagihanItem->tagihanLoloBatam ? $tagihanItem->tagihanLoloBatam->nomor_tagihan : null,
                    'tanggal_tagihan' => $tagihanItem && $tagihanItem->tagihanLoloBatam ? $tagihanItem->tagihanLoloBatam->tanggal_tagihan : null,
                    'tarif' => $tagihanItem ? $tagihanItem->tarif : null,
                ]);
            }
        }

        // Langsir Batam containers with LOLO = 1
        if ($sumber === 'all' || $sumber === 'langsir') {
            $langsirQuery = LangsirBatam::where('menggunakan_lolo', 1);

            if ($search && $activeTab !== 'faktur') {
                $langsirQuery->where(function ($q) use ($search) {
                    $q->where('no_kontainer', 'like', '%'.$search.'%')
                        ->orWhere('no_surat_jalan', 'like', '%'.$search.'%')
                        ->orWhere('no_transaksi', 'like', '%'.$search.'%')
                        ->orWhere('supir', 'like', '%'.$search.'%');
                });
            }

            if ($startDate && $activeTab !== 'faktur') {
                $langsirQuery->whereDate('tanggal', '>=', $startDate);
            }

            if ($endDate && $activeTab !== 'faktur') {
                $langsirQuery->whereDate('tanggal', '<=', $endDate);
            }

            $langsirs = $langsirQuery->latest('tanggal')->get();

            foreach ($langsirs as $l) {
                $isBilled = isset($billedLangsirs[$l->id]);
                $tagihanItem = $isBilled ? $billedLangsirs[$l->id] : null;

                if ($statusTagihan === 'belum' && $isBilled) {
                    continue;
                }
                if ($statusTagihan === 'sudah' && ! $isBilled) {
                    continue;
                }

                $operatorName = null;
                $tipeOperator = null;
                if ($tagihanItem && $tagihanItem->tagihanLoloBatam) {
                    $tlb = $tagihanItem->tagihanLoloBatam;
                    $tipeOperator = $tlb->tipe_operator;
                    $operatorName = $tlb->operator ?: ($tlb->operatorKaryawan->nama_lengkap ?? ($tipeOperator === 'VENDOR' ? $tlb->vendor : null));
                }

                $size = $l->size ?? '20';
                $matchedPl = $matchPricelist($size);
                $totalTarif = $isBilled && $tagihanItem ? ($tagihanItem->total ?? $tagihanItem->tarif) : ($matchedPl ? (float) $matchedPl->tarif : 0);
                $displaySize = (preg_replace('/[^0-9]/', '', (string) $size) ?: $size)."'";

                $containers->push((object) [
                    'sumber' => 'langsir',
                    'id' => $l->id,
                    'nomor_surat_jalan' => $l->no_surat_jalan ?? $l->no_transaksi,
                    'tanggal' => $l->tanggal,
                    'no_kontainer' => $l->no_kontainer,
                    'size' => $size,
                    'display_size' => $displaySize,
                    'total_tarif' => $totalTarif,
                    'formatted_total_tarif' => $totalTarif > 0 ? 'Rp '.number_format($totalTarif, 0, ',', '.') : '-',
                    'tipe_kontainer' => 'FULL',
                    'kapal' => '-',
                    'voyage' => '-',
                    'supir' => $l->supir ?? '-',
                    'no_plat' => $l->no_plat ?? '-',
                    'lokasi' => ($l->dari ?? '-').' -> '.($l->ke ?? '-'),
                    'is_billed' => $isBilled,
                    'tipe_operator' => $tipeOperator,
                    'operator' => $operatorName,
                    'tagihan_id' => $tagihanItem && $tagihanItem->tagihanLoloBatam ? $tagihanItem->tagihanLoloBatam->id : null,
                    'nomor_tagihan' => $tagihanItem && $tagihanItem->tagihanLoloBatam ? $tagihanItem->tagihanLoloBatam->nomor_tagihan : null,
                    'tanggal_tagihan' => $tagihanItem && $tagihanItem->tagihanLoloBatam ? $tagihanItem->tagihanLoloBatam->tanggal_tagihan : null,
                    'tarif' => $tagihanItem ? $tagihanItem->tarif : null,
                ]);
            }
        }

        // Sort all containers by date descending
        $containers = $containers->sortByDesc(function ($item) {
            return $item->tanggal ? strtotime($item->tanggal) : 0;
        })->values();

        // Container Statistics
        $totalBongkaranLolo = SuratJalanBongkaranBatam::where('menggunakan_lolo', 1)->count();
        $totalLangsirLolo = LangsirBatam::where('menggunakan_lolo', 1)->count();
        $totalKontainerLoloCount = $totalBongkaranLolo + $totalLangsirLolo;
        $unbilledBongkaranCount = SuratJalanBongkaranBatam::where('menggunakan_lolo', 1)->whereNotIn('id', $billedBongkarans->keys())->count();
        $unbilledLangsirCount = LangsirBatam::where('menggunakan_lolo', 1)->whereNotIn('id', $billedLangsirs->keys())->count();
        $countBelumDitagihKontainer = $unbilledBongkaranCount + $unbilledLangsirCount;
        $countSudahDitagihKontainer = $totalKontainerLoloCount - $countBelumDitagihKontainer;

        // Paginate containers collection
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('kontainer_page');
        $currentItems = $containers->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $pendingContainers = new LengthAwarePaginator(
            $currentItems,
            $containers->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'kontainer_page', 'query' => $request->query()]
        );

        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->distinct()->pluck('vendor');
        $kapals = SuratJalanBongkaranBatam::select('nama_kapal')->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal');

        // Generate automatic invoice number: 2 digit kode (LB) - 2 digit bulan - 2 digit tahun - 6 digit no urut
        $nomorTagihan = self::generateNomorPranota();

        $karyawanOperators = \Illuminate\Support\Facades\Schema::hasTable('karyawans')
            ? \App\Models\Karyawan::whereNull('tanggal_berhenti')
                ->where(function ($q) {
                    $q->whereIn('pekerjaan', ['OPERATOR FORKLIFT', 'OPERATOR CRANE / ALAT BERAT'])
                        ->orWhere('pekerjaan', 'like', '%FORKLIFT%')
                        ->orWhere('pekerjaan', 'like', '%CRANE%')
                        ->orWhere('pekerjaan', 'like', '%ALAT BERAT%');
                })
                ->orderBy('nama_lengkap')
                ->get(['id', 'nama_lengkap', 'nama_panggilan', 'divisi', 'pekerjaan'])
            : collect();

        return view('tagihan-lolo-batam.index', compact(
            'activeTab',
            'pendingContainers',
            'tagihans',
            'search',
            'statusPembayaran',
            'statusTagihan',
            'sumber',
            'vendor',
            'startDate',
            'endDate',
            'totalKontainerLoloCount',
            'countBelumDitagihKontainer',
            'countSudahDitagihKontainer',
            'totalTagihanCount',
            'totalNominal',
            'totalLunas',
            'totalBelumLunas',
            'countBelumLunas',
            'vendors',
            'kapals',
            'nomorTagihan',
            'pricelists',
            'karyawanOperators'
        ));
    }

    public static function generateNomorPranota(): string
    {
        $bulan = date('m');
        $tahun = date('y');
        $prefix = "LB-{$bulan}-{$tahun}-";

        $lastInvoice = TagihanLoloBatam::withTrashed()
            ->where('nomor_tagihan', 'like', $prefix.'%')
            ->orderBy('nomor_tagihan', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNum = (int) substr($lastInvoice->nomor_tagihan, -6);
            $nextNum = str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '000001';
        }

        return $prefix.$nextNum;
    }

    public function create(Request $request)
    {
        $pricelists = MasterPricelistLoloBatam::aktif()->orderBy('size')->get();
        $karyawanOperators = \Illuminate\Support\Facades\Schema::hasTable('karyawans')
            ? \App\Models\Karyawan::whereNull('tanggal_berhenti')
                ->where(function ($q) {
                    $q->whereIn('pekerjaan', ['OPERATOR FORKLIFT', 'OPERATOR CRANE / ALAT BERAT'])
                        ->orWhere('pekerjaan', 'like', '%FORKLIFT%')
                        ->orWhere('pekerjaan', 'like', '%CRANE%')
                        ->orWhere('pekerjaan', 'like', '%ALAT BERAT%');
                })
                ->orderBy('nama_lengkap')
                ->get(['id', 'nama_lengkap', 'nama_panggilan', 'divisi', 'pekerjaan'])
            : collect();

        // Generate automatic invoice number: 2 digit kode (LB) - 2 digit bulan - 2 digit tahun - 6 digit no urut
        $nomorTagihan = self::generateNomorPranota();

        // Existing vendors from previous invoices
        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->distinct()->pluck('vendor');

        // Kapal list from recent Bongkaran Batam
        $kapals = SuratJalanBongkaranBatam::select('nama_kapal')->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal');

        // Handle operator parameters from query
        $defaultTipeOperator = $request->get('tipe_operator', 'AYP');
        $defaultOperatorKaryawanId = $request->get('operator_karyawan_id');
        $defaultOperator = $request->get('operator');
        $operatorsInput = $request->get('operators', []);

        // Handle preloaded items from Bongkaran or Langsir query parameters
        $preloadedItems = [];
        $defaultKapal = null;
        $defaultVoyage = null;

        $matchPricelist = function ($containerSize, $containerTipe = 'FULL') use ($pricelists) {
            if (empty($containerSize)) {
                return $pricelists->firstWhere('size', '20') ?? $pricelists->first();
            }

            // 1. Exact match
            $exact = $pricelists->first(function ($p) use ($containerSize) {
                return strcasecmp(trim($p->size), trim($containerSize)) === 0;
            });
            if ($exact) {
                return $exact;
            }

            // 2. Numeric digits match (e.g. '20FT' matches '20')
            $numSize = preg_replace('/[^0-9]/', '', (string) $containerSize);
            if ($numSize !== '') {
                $numMatch = $pricelists->first(function ($p) use ($numSize) {
                    $pNum = preg_replace('/[^0-9]/', '', (string) $p->size);

                    return $pNum !== '' && $pNum === $numSize;
                });
                if ($numMatch) {
                    return $numMatch;
                }
            }

            // 3. Substring match
            return $pricelists->first(function ($p) use ($containerSize) {
                return stripos($containerSize, (string) $p->size) !== false || stripos((string) $p->size, (string) $containerSize) !== false;
            });
        };

        $bongkaranIds = [];
        if ($request->filled('bongkaran_ids')) {
            $bongkaranIds = is_array($request->bongkaran_ids) ? $request->bongkaran_ids : explode(',', $request->bongkaran_ids);
        } elseif ($request->filled('bongkaran_id')) {
            $bongkaranIds = [$request->bongkaran_id];
        }

        if (! empty($bongkaranIds)) {
            $bongkarans = SuratJalanBongkaranBatam::whereIn('id', $bongkaranIds)->get();
            foreach ($bongkarans as $b) {
                $size = $b->size ?? '20';
                $tipe = $b->tipe_kontainer ?? ($b->f_e ? strtoupper($b->f_e) : 'FULL');

                $matchedPl = $matchPricelist($size, $tipe);

                $opInfo = $operatorsInput['bongkaran'][$b->id] ?? null;
                $itemTipeOp = $opInfo['tipe'] ?? $defaultTipeOperator;
                $itemOpKaryawanId = ! empty($opInfo['karyawan_id']) ? $opInfo['karyawan_id'] : $defaultOperatorKaryawanId;
                $itemOpName = $opInfo['vendor_nama'] ?? $defaultOperator;
                if ($itemTipeOp === 'AYP' && $itemOpKaryawanId) {
                    $karyawan = $karyawanOperators->firstWhere('id', $itemOpKaryawanId);
                    if ($karyawan) {
                        $itemOpName = $karyawan->nama_lengkap;
                    }
                }

                if (! $defaultOperatorKaryawanId && $itemOpKaryawanId) {
                    $defaultOperatorKaryawanId = $itemOpKaryawanId;
                    $defaultTipeOperator = $itemTipeOp;
                    $defaultOperator = $itemOpName;
                } elseif (! $defaultOperator && $itemOpName) {
                    $defaultTipeOperator = $itemTipeOp;
                    $defaultOperator = $itemOpName;
                }

                $preloadedItems[] = [
                    'sumber_data' => 'bongkaran',
                    'surat_jalan_bongkaran_id' => $b->id,
                    'langsir_batam_id' => null,
                    'master_pricelist_lolo_batam_id' => $matchedPl ? $matchedPl->id : null,
                    'nomor_surat_jalan' => $b->nomor_surat_jalan,
                    'nomor_kontainer' => $b->no_kontainer,
                    'size' => $size,
                    'tipe_kontainer' => $tipe,
                    'tipe_operator' => $itemTipeOp,
                    'operator_karyawan_id' => $itemOpKaryawanId,
                    'operator' => $itemOpName,
                    'kegiatan' => 'LOLO Bongkaran Batam ('.($b->nomor_surat_jalan ?? '-').')',
                    'tarif' => $matchedPl ? $matchedPl->tarif : 0,
                    'jumlah' => 1,
                    'keterangan' => 'Kapal: '.($b->nama_kapal ?? '-').' Voy: '.($b->no_voyage ?? '-'),
                ];

                if (! $defaultKapal && $b->nama_kapal) {
                    $defaultKapal = $b->nama_kapal;
                }
                if (! $defaultVoyage && $b->no_voyage) {
                    $defaultVoyage = $b->no_voyage;
                }
            }
        }

        $langsirIds = [];
        if ($request->filled('langsir_ids')) {
            $langsirIds = is_array($request->langsir_ids) ? $request->langsir_ids : explode(',', $request->langsir_ids);
        } elseif ($request->filled('langsir_id')) {
            $langsirIds = [$request->langsir_id];
        }

        if (! empty($langsirIds)) {
            $langsirs = LangsirBatam::whereIn('id', $langsirIds)->get();
            foreach ($langsirs as $l) {
                $size = $l->size ?? '20';
                $tipe = 'FULL';

                $matchedPl = $matchPricelist($size, $tipe);

                $opInfo = $operatorsInput['langsir'][$l->id] ?? null;
                $itemTipeOp = $opInfo['tipe'] ?? $defaultTipeOperator;
                $itemOpKaryawanId = ! empty($opInfo['karyawan_id']) ? $opInfo['karyawan_id'] : $defaultOperatorKaryawanId;
                $itemOpName = $opInfo['vendor_nama'] ?? $defaultOperator;
                if ($itemTipeOp === 'AYP' && $itemOpKaryawanId) {
                    $karyawan = $karyawanOperators->firstWhere('id', $itemOpKaryawanId);
                    if ($karyawan) {
                        $itemOpName = $karyawan->nama_lengkap;
                    }
                }

                if (! $defaultOperatorKaryawanId && $itemOpKaryawanId) {
                    $defaultOperatorKaryawanId = $itemOpKaryawanId;
                    $defaultTipeOperator = $itemTipeOp;
                    $defaultOperator = $itemOpName;
                } elseif (! $defaultOperator && $itemOpName) {
                    $defaultTipeOperator = $itemTipeOp;
                    $defaultOperator = $itemOpName;
                }

                $preloadedItems[] = [
                    'sumber_data' => 'langsir',
                    'surat_jalan_bongkaran_id' => null,
                    'langsir_batam_id' => $l->id,
                    'master_pricelist_lolo_batam_id' => $matchedPl ? $matchedPl->id : null,
                    'nomor_surat_jalan' => $l->no_surat_jalan ?? $l->no_transaksi,
                    'nomor_kontainer' => $l->no_kontainer,
                    'size' => $size,
                    'tipe_kontainer' => $tipe,
                    'tipe_operator' => $itemTipeOp,
                    'operator_karyawan_id' => $itemOpKaryawanId,
                    'operator' => $itemOpName,
                    'kegiatan' => 'LOLO Langsir Batam ('.($l->dari ?? '-').' -> '.($l->ke ?? '-').')',
                    'tarif' => $matchedPl ? $matchedPl->tarif : 0,
                    'jumlah' => 1,
                    'keterangan' => 'Supir: '.($l->supir ?? '-').' Plat: '.($l->no_plat ?? '-'),
                ];
            }
        }

        return view('tagihan-lolo-batam.create', compact(
            'pricelists',
            'nomorTagihan',
            'vendors',
            'kapals',
            'preloadedItems',
            'defaultKapal',
            'defaultVoyage',
            'karyawanOperators',
            'defaultTipeOperator',
            'defaultOperatorKaryawanId',
            'defaultOperator'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_tagihan' => 'required|string|unique:tagihan_lolo_batams,nomor_tagihan',
            'tanggal_tagihan' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'tipe_operator' => 'nullable|in:AYP,VENDOR',
            'operator_karyawan_id' => 'nullable|exists:karyawans,id',
            'operator' => 'nullable|string|max:255',
            'kapal' => 'nullable|string|max:255',
            'voyage' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|required_if:status_pembayaran,Lunas|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nomor_kontainer' => 'required|string|max:255',
            'items.*.size' => 'nullable|string|max:10',
            'items.*.tipe_kontainer' => 'nullable|string|max:50',
            'items.*.kegiatan' => 'nullable|string|max:255',
            'items.*.sumber_data' => 'nullable|string|in:bongkaran,langsir,manual',
            'items.*.surat_jalan_bongkaran_id' => 'nullable|integer',
            'items.*.langsir_batam_id' => 'nullable|integer',
            'items.*.master_pricelist_lolo_batam_id' => 'nullable|integer',
            'items.*.nomor_surat_jalan' => 'nullable|string|max:255',
            'items.*.tarif' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalTagihan = 0;
            $itemsData = [];

            $tipeOperator = $request->tipe_operator ?: 'AYP';
            $operatorKaryawanId = $request->operator_karyawan_id;
            $operatorName = $request->operator;

            if ($tipeOperator === 'AYP') {
                if ($operatorKaryawanId) {
                    $karyawan = \App\Models\Karyawan::find($operatorKaryawanId);
                    if ($karyawan) {
                        $operatorName = $karyawan->nama_lengkap;
                    }
                }
            } else {
                $operatorKaryawanId = null;
                if (empty($operatorName)) {
                    $operatorName = $request->vendor ?: 'Vendor';
                }
            }

            foreach ($request->items as $item) {
                $subtotal = (float) $item['tarif'] * (int) $item['jumlah'];
                $totalTagihan += $subtotal;

                $itemsData[] = [
                    'sumber_data' => $item['sumber_data'] ?? 'manual',
                    'surat_jalan_bongkaran_id' => ! empty($item['surat_jalan_bongkaran_id']) ? $item['surat_jalan_bongkaran_id'] : null,
                    'langsir_batam_id' => ! empty($item['langsir_batam_id']) ? $item['langsir_batam_id'] : null,
                    'master_pricelist_lolo_batam_id' => ! empty($item['master_pricelist_lolo_batam_id']) ? $item['master_pricelist_lolo_batam_id'] : null,
                    'nomor_surat_jalan' => $item['nomor_surat_jalan'] ?? null,
                    'nomor_kontainer' => strtoupper(trim($item['nomor_kontainer'])),
                    'size' => $item['size'] ?? '20',
                    'tipe_kontainer' => $item['tipe_kontainer'] ?? 'FULL',
                    'kegiatan' => $item['kegiatan'] ?? 'LOLO Batam',
                    'tipe_operator' => $tipeOperator,
                    'operator' => $operatorName,
                    'operator_karyawan_id' => $operatorKaryawanId,
                    'tarif' => $item['tarif'],
                    'jumlah' => $item['jumlah'],
                    'total' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $tagihan = TagihanLoloBatam::create([
                'nomor_tagihan' => $request->nomor_tagihan,
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'vendor' => $request->vendor,
                'tipe_operator' => $tipeOperator,
                'operator' => $operatorName,
                'operator_karyawan_id' => $operatorKaryawanId,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_bayar' => $request->status_pembayaran === 'Lunas' ? $request->tanggal_bayar : null,
                'total_tagihan' => $totalTagihan,
                'keterangan' => $request->keterangan,
                'status_approval' => 'Pending',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($itemsData as $row) {
                $row['tagihan_lolo_batam_id'] = $tagihan->id;
                TagihanLoloBatamItem::create($row);
            }

            DB::commit();

            return redirect()->route('tagihan-lolo-batam.show', $tagihan->id)
                ->with('success', 'Tagihan LOLO Batam '.$tagihan->nomor_tagihan.' berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menyimpan Tagihan LOLO Batam: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show(TagihanLoloBatam $tagihanLoloBatam)
    {
        $tagihanLoloBatam->load(['items.pricelistLoloBatam', 'createdBy', 'updatedBy', 'operatorKaryawan']);

        return view('tagihan-lolo-batam.show', compact('tagihanLoloBatam'));
    }

    public function edit(TagihanLoloBatam $tagihanLoloBatam)
    {
        $tagihanLoloBatam->load(['items', 'operatorKaryawan']);
        $pricelists = MasterPricelistLoloBatam::aktif()->orderBy('size')->get();
        $vendors = TagihanLoloBatam::select('vendor')->whereNotNull('vendor')->where('vendor', '!=', '')->distinct()->pluck('vendor');
        $kapals = SuratJalanBongkaranBatam::select('nama_kapal')->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal');
        $karyawanOperators = \Illuminate\Support\Facades\Schema::hasTable('karyawans')
            ? \App\Models\Karyawan::whereNull('tanggal_berhenti')
                ->where(function ($q) {
                    $q->whereIn('pekerjaan', ['OPERATOR FORKLIFT', 'OPERATOR CRANE / ALAT BERAT'])
                        ->orWhere('pekerjaan', 'like', '%FORKLIFT%')
                        ->orWhere('pekerjaan', 'like', '%CRANE%')
                        ->orWhere('pekerjaan', 'like', '%ALAT BERAT%');
                })
                ->orderBy('nama_lengkap')
                ->get(['id', 'nama_lengkap', 'nama_panggilan', 'divisi', 'pekerjaan'])
            : collect();

        return view('tagihan-lolo-batam.edit', compact('tagihanLoloBatam', 'pricelists', 'vendors', 'kapals', 'karyawanOperators'));
    }

    public function update(Request $request, TagihanLoloBatam $tagihanLoloBatam)
    {
        $validator = Validator::make($request->all(), [
            'nomor_tagihan' => 'required|string|unique:tagihan_lolo_batams,nomor_tagihan,'.$tagihanLoloBatam->id,
            'tanggal_tagihan' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'tipe_operator' => 'nullable|in:AYP,VENDOR',
            'operator_karyawan_id' => 'nullable|exists:karyawans,id',
            'operator' => 'nullable|string|max:255',
            'kapal' => 'nullable|string|max:255',
            'voyage' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|required_if:status_pembayaran,Lunas|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nomor_kontainer' => 'required|string|max:255',
            'items.*.size' => 'nullable|string|max:10',
            'items.*.tipe_kontainer' => 'nullable|string|max:50',
            'items.*.kegiatan' => 'nullable|string|max:255',
            'items.*.sumber_data' => 'nullable|string|in:bongkaran,langsir,manual',
            'items.*.surat_jalan_bongkaran_id' => 'nullable|integer',
            'items.*.langsir_batam_id' => 'nullable|integer',
            'items.*.master_pricelist_lolo_batam_id' => 'nullable|integer',
            'items.*.nomor_surat_jalan' => 'nullable|string|max:255',
            'items.*.tarif' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalTagihan = 0;
            $itemsData = [];

            $tipeOperator = $request->tipe_operator ?: 'AYP';
            $operatorKaryawanId = $request->operator_karyawan_id;
            $operatorName = $request->operator;

            if ($tipeOperator === 'AYP') {
                if ($operatorKaryawanId) {
                    $karyawan = \App\Models\Karyawan::find($operatorKaryawanId);
                    if ($karyawan) {
                        $operatorName = $karyawan->nama_lengkap;
                    }
                }
            } else {
                $operatorKaryawanId = null;
                if (empty($operatorName)) {
                    $operatorName = $request->vendor ?: 'Vendor';
                }
            }

            foreach ($request->items as $item) {
                $subtotal = (float) $item['tarif'] * (int) $item['jumlah'];
                $totalTagihan += $subtotal;

                $itemsData[] = [
                    'tagihan_lolo_batam_id' => $tagihanLoloBatam->id,
                    'sumber_data' => $item['sumber_data'] ?? 'manual',
                    'surat_jalan_bongkaran_id' => ! empty($item['surat_jalan_bongkaran_id']) ? $item['surat_jalan_bongkaran_id'] : null,
                    'langsir_batam_id' => ! empty($item['langsir_batam_id']) ? $item['langsir_batam_id'] : null,
                    'master_pricelist_lolo_batam_id' => ! empty($item['master_pricelist_lolo_batam_id']) ? $item['master_pricelist_lolo_batam_id'] : null,
                    'nomor_surat_jalan' => $item['nomor_surat_jalan'] ?? null,
                    'nomor_kontainer' => strtoupper(trim($item['nomor_kontainer'])),
                    'size' => $item['size'] ?? '20',
                    'tipe_kontainer' => $item['tipe_kontainer'] ?? 'FULL',
                    'kegiatan' => $item['kegiatan'] ?? 'LOLO Batam',
                    'tipe_operator' => $tipeOperator,
                    'operator' => $operatorName,
                    'operator_karyawan_id' => $operatorKaryawanId,
                    'tarif' => $item['tarif'],
                    'jumlah' => $item['jumlah'],
                    'total' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $tagihanLoloBatam->update([
                'nomor_tagihan' => $request->nomor_tagihan,
                'tanggal_tagihan' => $request->tanggal_tagihan,
                'vendor' => $request->vendor,
                'tipe_operator' => $tipeOperator,
                'operator' => $operatorName,
                'operator_karyawan_id' => $operatorKaryawanId,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'status_pembayaran' => $request->status_pembayaran,
                'tanggal_bayar' => $request->status_pembayaran === 'Lunas' ? $request->tanggal_bayar : null,
                'total_tagihan' => $totalTagihan,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            // Replace items
            $tagihanLoloBatam->items()->delete();
            foreach ($itemsData as $row) {
                TagihanLoloBatamItem::create($row);
            }

            DB::commit();

            return redirect()->route('tagihan-lolo-batam.show', $tagihanLoloBatam->id)
                ->with('success', 'Tagihan LOLO Batam '.$tagihanLoloBatam->nomor_tagihan.' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui Tagihan LOLO Batam: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(TagihanLoloBatam $tagihanLoloBatam)
    {
        $nomor = $tagihanLoloBatam->nomor_tagihan;
        $tagihanLoloBatam->delete();

        return redirect()->route('tagihan-lolo-batam.index')
            ->with('success', 'Tagihan LOLO Batam '.$nomor.' berhasil dihapus.');
    }

    public function print(TagihanLoloBatam $tagihanLoloBatam)
    {
        $tagihanLoloBatam->load(['items.pricelistLoloBatam', 'createdBy']);

        return view('tagihan-lolo-batam.print', compact('tagihanLoloBatam'));
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $statusPembayaran = $request->get('status_pembayaran');

        $query = TagihanLoloBatam::with(['items', 'createdBy'])->latest('tanggal_tagihan');

        if ($search) {
            $query->search($search);
        }

        if ($statusPembayaran) {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        $tagihans = $query->get();

        $filename = 'tagihan_lolo_batam_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($tagihans) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, [
                'No',
                'Nomor Tagihan',
                'Tanggal Tagihan',
                'Vendor',
                'Kapal',
                'Voyage',
                'Jumlah Item Kontainer',
                'Total Tagihan (Rp)',
                'Status Pembayaran',
                'Tanggal Bayar',
                'Keterangan',
                'Dibuat Oleh',
            ]);

            foreach ($tagihans as $idx => $tagihan) {
                fputcsv($handle, [
                    $idx + 1,
                    $tagihan->nomor_tagihan,
                    $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d/m/Y') : '-',
                    $tagihan->vendor ?? '-',
                    $tagihan->kapal ?? '-',
                    $tagihan->voyage ?? '-',
                    $tagihan->items->count(),
                    $tagihan->total_tagihan,
                    $tagihan->status_pembayaran,
                    $tagihan->tanggal_bayar ? $tagihan->tanggal_bayar->format('d/m/Y') : '-',
                    $tagihan->keterangan ?? '-',
                    $tagihan->createdBy ? $tagihan->createdBy->name ?? $tagihan->createdBy->username : '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API to search and retrieve unbilled containers with LOLO = 1 from Bongkaran Batam & Langsir Batam
     */
    public function getPendingLolo(Request $request)
    {
        $type = $request->get('type', 'all'); // 'bongkaran', 'langsir', 'all'
        $search = $request->get('search');

        $billedBongkaranIds = TagihanLoloBatamItem::whereNotNull('surat_jalan_bongkaran_id')->pluck('surat_jalan_bongkaran_id')->toArray();
        $billedLangsirIds = TagihanLoloBatamItem::whereNotNull('langsir_batam_id')->pluck('langsir_batam_id')->toArray();

        $results = [];

        if ($type === 'all' || $type === 'bongkaran') {
            $bongkaranQuery = SuratJalanBongkaranBatam::where('menggunakan_lolo', 1)
                ->whereNotIn('id', $billedBongkaranIds);

            if ($search) {
                $bongkaranQuery->where(function ($q) use ($search) {
                    $q->where('no_kontainer', 'like', '%'.$search.'%')
                        ->orWhere('nomor_surat_jalan', 'like', '%'.$search.'%')
                        ->orWhere('nama_kapal', 'like', '%'.$search.'%');
                });
            }

            $bongkarans = $bongkaranQuery->latest('tanggal_surat_jalan')->limit(50)->get();

            foreach ($bongkarans as $b) {
                $results[] = [
                    'sumber_data' => 'bongkaran',
                    'id' => $b->id,
                    'nomor_surat_jalan' => $b->nomor_surat_jalan,
                    'nomor_kontainer' => $b->no_kontainer,
                    'size' => $b->size ?? '20',
                    'tipe_kontainer' => $b->tipe_kontainer ?? ($b->f_e ? strtoupper($b->f_e) : 'FULL'),
                    'kapal' => $b->nama_kapal,
                    'voyage' => $b->no_voyage,
                    'tanggal' => $b->tanggal_surat_jalan ? date('d/m/Y', strtotime($b->tanggal_surat_jalan)) : '-',
                    'kegiatan' => 'LOLO Bongkaran Batam ('.($b->nomor_surat_jalan ?? '-').')',
                ];
            }
        }

        if ($type === 'all' || $type === 'langsir') {
            $langsirQuery = LangsirBatam::where('menggunakan_lolo', 1)
                ->whereNotIn('id', $billedLangsirIds);

            if ($search) {
                $langsirQuery->where(function ($q) use ($search) {
                    $q->where('no_kontainer', 'like', '%'.$search.'%')
                        ->orWhere('no_surat_jalan', 'like', '%'.$search.'%')
                        ->orWhere('no_transaksi', 'like', '%'.$search.'%');
                });
            }

            $langsirs = $langsirQuery->latest('tanggal')->limit(50)->get();

            foreach ($langsirs as $l) {
                $results[] = [
                    'sumber_data' => 'langsir',
                    'id' => $l->id,
                    'nomor_surat_jalan' => $l->no_surat_jalan ?? $l->no_transaksi,
                    'nomor_kontainer' => $l->no_kontainer,
                    'size' => $l->size ?? '20',
                    'tipe_kontainer' => 'FULL',
                    'kapal' => '-',
                    'voyage' => '-',
                    'tanggal' => $l->tanggal ? date('d/m/Y', strtotime($l->tanggal)) : '-',
                    'kegiatan' => 'LOLO Langsir Batam ('.($l->dari ?? '-').' -> '.($l->ke ?? '-').')',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => count($results),
        ]);
    }
}
