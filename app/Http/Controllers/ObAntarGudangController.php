<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\Kontainer;
use App\Models\HistoryKontainer;
use App\Models\MasterPricelistObAntarGudang;
use App\Models\StockKontainer;
use App\Models\TagihanOb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ObAntarGudangController extends Controller
{
    private const COMBO_BIAYA_20FT_SERVICE = 37500;

    /**
     * Return the warehouse where a container was located on a given date.
     */
    public function gudangAsal(Request $request)
    {
        $validated = $request->validate([
            'nomor_kontainer' => 'required|string',
            'tanggal_ob' => 'required|date',
        ]);

        $history = HistoryKontainer::where('nomor_kontainer', $validated['nomor_kontainer'])
            ->whereDate('tanggal_kegiatan', '<=', $validated['tanggal_ob'])
            ->whereNotNull('gudang_id')
            ->orderByDesc('tanggal_kegiatan')
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'gudang_id' => $history?->gudang_id,
            'history_date' => $history?->tanggal_kegiatan?->format('Y-m-d'),
        ]);
    }

    /**
     * Display the index page with kontainer data.
     */
    public function index(Request $request)
    {
        $gudangId = $request->input('gudang_id');

        $gudang = $gudangId ? Gudang::find($gudangId) : null;
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        // Build queries with filters
        $search = $request->input('search');
        $filterStatus = $request->input('status');
        $filterUkuran = $request->input('ukuran');
        $filterTipe = $request->input('tipe_kontainer');

        // Query stock_kontainers
        $stockKontainersQuery = StockKontainer::query();
        if ($gudangId) {
            $stockKontainersQuery->where('gudangs_id', $gudangId);
        }

        if ($search) {
            $stockKontainersQuery->where(function ($q) use ($search) {
                $q->where('nomor_seri_gabungan', 'like', "%{$search}%")
                    ->orWhere('awalan_kontainer', 'like', "%{$search}%")
                    ->orWhere('nomor_seri_kontainer', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $stockKontainersQuery->where('status', $filterStatus);
        }

        if ($filterUkuran) {
            $stockKontainersQuery->where('ukuran', $filterUkuran);
        }

        if ($filterTipe) {
            $stockKontainersQuery->where('tipe_kontainer', $filterTipe);
        }

        $stockKontainers = $stockKontainersQuery->with('gudang')
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'stock_page')
            ->appends($request->query());

        // Query kontainers
        $kontainersQuery = Kontainer::query();
        if ($gudangId) {
            $kontainersQuery->where('gudangs_id', $gudangId);
        }

        if ($search) {
            $kontainersQuery->where(function ($q) use ($search) {
                $q->where('nomor_seri_gabungan', 'like', "%{$search}%")
                    ->orWhere('awalan_kontainer', 'like', "%{$search}%")
                    ->orWhere('nomor_seri_kontainer', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $kontainersQuery->where('status', $filterStatus);
        }

        if ($filterUkuran) {
            $kontainersQuery->where('ukuran', $filterUkuran);
        }

        if ($filterTipe) {
            $kontainersQuery->where('tipe_kontainer', $filterTipe);
        }

        $kontainers = $kontainersQuery->with('gudang')
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'kontainer_page')
            ->appends($request->query());

        // Stats
        $stockStatsQuery = StockKontainer::query();
        $kontainerStatsQuery = Kontainer::query();
        if ($gudangId) {
            $stockStatsQuery->where('gudangs_id', $gudangId);
            $kontainerStatsQuery->where('gudangs_id', $gudangId);
        }

        $totalStockKontainers = (clone $stockStatsQuery)->count();
        $totalKontainers = (clone $kontainerStatsQuery)->count();
        $totalAll = $totalStockKontainers + $totalKontainers;

        // Size breakdown
        $stockSizes = (clone $stockStatsQuery)
            ->selectRaw('ukuran, COUNT(*) as total')
            ->groupBy('ukuran')
            ->pluck('total', 'ukuran')
            ->toArray();

        $kontainerSizes = (clone $kontainerStatsQuery)
            ->selectRaw('ukuran, COUNT(*) as total')
            ->groupBy('ukuran')
            ->pluck('total', 'ukuran')
            ->toArray();

        // Fetch supirs for the modal
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nama_panggilan']);

        // Fetch pricelists for Harga OB logic, including destination-specific rates.
        $pricelists = MasterPricelistObAntarGudang::with('gudangTujuan')
            ->orderBy('size_kontainer')
            ->orderBy('gudang_tujuan_id')
            ->orderBy('status_kontainer')
            ->orderBy('status_service')
            ->get();

        return view('ob-antar-gudang.index', compact(
            'gudang',
            'gudangs',
            'stockKontainers',
            'kontainers',
            'totalStockKontainers',
            'totalKontainers',
            'totalAll',
            'stockSizes',
            'kontainerSizes',
            'search',
            'filterStatus',
            'filterUkuran',
            'filterTipe',
            'supirs',
            'pricelists'
        ));
    }

    /**
     * Store a newly created tagihan ob antar gudang.
     */
    public function storeTagihan(Request $request)
    {
        $validated = $request->validate([
            'tanggal_ob' => 'required|date',
            'nomor_kontainer' => 'required|string',
            'ukuran' => 'required|string',
            'nama_supir' => 'required|string',
            'pricelist_id' => 'required|exists:master_pricelist_ob_antar_gudang,id',
            'status_service' => 'required|in:service,non_service',
            'status_kontainer' => [
                'nullable',
                'in:full,empty',
                Rule::requiredIf(fn () => $request->input('status_service') !== 'service'),
            ],
            'is_combo' => 'sometimes|boolean',
            'nominal' => 'required|numeric|min:0',
            'gudang_id' => 'required|exists:gudangs,id',
            'gudang_tujuan_id' => 'required|exists:gudangs,id',
            'source' => 'required|in:stock,kontainer',
            'keterangan' => 'nullable|string',
        ]);

        $ukuran = preg_replace('/\s+/', '', str_ireplace('ft', '', $validated['ukuran']));
        $validated['is_combo'] = $request->boolean('is_combo');
        $gudangTujuan = Gudang::findOrFail($validated['gudang_tujuan_id']);
        $namaGudangTujuan = mb_strtolower($gudangTujuan->nama_gudang);
        $abaikanStatusKontainer = str_contains($namaGudangTujuan, 'temas')
            && ! str_contains($namaGudangTujuan, 'temas jkt');

        if ($validated['is_combo'] && ($ukuran !== '20' || $validated['status_service'] !== 'service')) {
            return back()->withInput()->with('error', 'Combo hanya tersedia untuk kontainer 20 ft dengan status Service.');
        }

        $pricelistDimensions = MasterPricelistObAntarGudang::where('size_kontainer', $ukuran.'ft')
            ->where('status_service', $validated['status_service']);

        if ($validated['status_service'] === 'service') {
            $pricelistDimensions->whereNull('status_kontainer');
            $validated['status_kontainer'] = null;
        } elseif (! $abaikanStatusKontainer) {
            $pricelistDimensions->where('status_kontainer', $validated['status_kontainer']);
        }

        $destinationPricelists = (clone $pricelistDimensions)
            ->where('gudang_tujuan_id', $validated['gudang_tujuan_id'])
            ->pluck('id');
        $pricelistQuery = MasterPricelistObAntarGudang::whereKey($validated['pricelist_id'])
            ->where('size_kontainer', $ukuran.'ft')
            ->where('status_service', $validated['status_service']);
        if ($validated['status_service'] === 'service') {
            $pricelistQuery->whereNull('status_kontainer');
        } elseif (! $abaikanStatusKontainer) {
            $pricelistQuery->where('status_kontainer', $validated['status_kontainer']);
        }
        if ($destinationPricelists->isNotEmpty()) {
            $pricelistQuery->where('gudang_tujuan_id', $validated['gudang_tujuan_id']);
        } else {
            $pricelistQuery->whereNull('gudang_tujuan_id');
        }
        $pricelist = $pricelistQuery->first();

        if (! $pricelist || ($destinationPricelists->isNotEmpty() && ! $destinationPricelists->contains($pricelist->id))) {
            return back()->withInput()->with('error', 'Pricelist tidak sesuai dengan ukuran, status, dan gudang tujuan. Tarif khusus tujuan akan digunakan jika tersedia.');
        }

        try {
            DB::beginTransaction();

            $gudangAsal = Gudang::find($validated['gudang_id']);
            // The origin must be the container's historical position on the OB date.
            $historyGudangId = HistoryKontainer::where('nomor_kontainer', $validated['nomor_kontainer'])
                ->whereDate('tanggal_kegiatan', '<=', $validated['tanggal_ob'])
                ->whereNotNull('gudang_id')
                ->orderByDesc('tanggal_kegiatan')
                ->orderByDesc('id')
                ->value('gudang_id');

            if ($historyGudangId) {
                $validated['gudang_id'] = $historyGudangId;
                $gudangAsal = Gudang::find($historyGudangId);
            }

            $tagihan = new TagihanOb;
            $tagihan->tanggal_ob = $validated['tanggal_ob'];
            $tagihan->kapal = 'ANTAR GUDANG';
            $tagihan->voyage = 'ANTAR GUDANG';
            $tagihan->kegiatan = 'ANTAR GUDANG';
            $tagihan->nomor_kontainer = $validated['nomor_kontainer'];
            $tagihan->size_kontainer = $validated['ukuran'];
            $tagihan->nama_supir = $validated['nama_supir'];
            $tagihan->status_kontainer = $validated['status_kontainer'];
            $tagihan->is_combo = $validated['is_combo'];
            $tagihan->barang = 'KOSONGAN / ISI (ANTAR GUDANG)';
            $tagihan->keterangan = $validated['keterangan']
                ?? ('Antar Gudang: '.($gudangAsal->nama_gudang ?? '-').' → '.($gudangTujuan->nama_gudang ?? '-'));
            $tagihan->created_by = Auth::id();

            // Use the selected pricelist as the authoritative OB price.
            $tagihan->biaya = $validated['is_combo']
                ? self::COMBO_BIAYA_20FT_SERVICE
                : $pricelist->biaya;

            $tagihan->save();

            // Update gudang_id pada kontainer terkait (pindahkan ke gudang tujuan)
            if ($validated['source'] === 'stock') {
                $stockModel = StockKontainer::where(function ($q) use ($validated) {
                        $q->where('nomor_seri_gabungan', $validated['nomor_kontainer'])
                            ->orWhere(DB::raw('CONCAT(awalan_kontainer, nomor_seri_kontainer)'), $validated['nomor_kontainer']);
                    })->first();

                if ($stockModel) {
                    $asalGudangId = $validated['gudang_id'];
                    $stockModel->update(['gudangs_id' => $validated['gudang_tujuan_id']]);

                    // Record history
                    \App\Models\HistoryKontainer::create([
                        'nomor_kontainer' => $validated['nomor_kontainer'],
                        'tipe_kontainer' => 'stock',
                        'jenis_kegiatan' => 'Pindahan Gudang',
                        'tanggal_kegiatan' => $validated['tanggal_ob'],
                        'asal_gudang_id' => $asalGudangId,
                        'gudang_id' => $validated['gudang_tujuan_id'],
                        'keterangan' => 'OB Antar Gudang: '.($gudangAsal->nama_gudang ?? '-').' -> '.($gudangTujuan->nama_gudang ?? '-'),
                        'created_by' => Auth::id(),
                    ]);
                }
            } else {
                $kontainerModel = Kontainer::where(function ($q) use ($validated) {
                        $q->where('nomor_seri_gabungan', $validated['nomor_kontainer'])
                            ->orWhere(DB::raw('CONCAT(awalan_kontainer, nomor_seri_kontainer)'), $validated['nomor_kontainer']);
                    })->first();

                if ($kontainerModel) {
                    $asalGudangId = $validated['gudang_id'];
                    $kontainerModel->update(['gudangs_id' => $validated['gudang_tujuan_id']]);

                    // Record history
                    \App\Models\HistoryKontainer::create([
                        'nomor_kontainer' => $validated['nomor_kontainer'],
                        'tipe_kontainer' => 'kontainer',
                        'jenis_kegiatan' => 'Pindahan Gudang',
                        'tanggal_kegiatan' => $validated['tanggal_ob'],
                        'asal_gudang_id' => $asalGudangId,
                        'gudang_id' => $validated['gudang_tujuan_id'],
                        'keterangan' => 'OB Antar Gudang: '.($gudangAsal->nama_gudang ?? '-').' -> '.($gudangTujuan->nama_gudang ?? '-'),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Tagihan OB Antar Gudang berhasil dibuat. Kontainer '.$validated['nomor_kontainer'].' dipindahkan ke '.($gudangTujuan->nama_gudang ?? 'gudang tujuan').'.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal membuat tagihan: '.$e->getMessage());
        }
    }
}
