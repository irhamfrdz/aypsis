<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\StockKontainer;
use App\Models\Kontainer;
use App\Models\Karyawan;
use App\Models\TagihanOb;
use App\Models\MasterPricelistOb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ObAntarGudangController extends Controller
{
    /**
     * Display the select gudang page.
     */
    public function select()
    {
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        return view('ob-antar-gudang.select', compact('gudangs'));
    }

    /**
     * Display the index page with kontainer data for selected gudang.
     */
    public function index(Request $request)
    {
        $gudangId = $request->input('gudang_id');
        
        if (!$gudangId) {
            return redirect()->route('ob-antar-gudang.select')
                ->with('error', 'Silakan pilih gudang terlebih dahulu.');
        }

        $gudang = Gudang::findOrFail($gudangId);
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        // Build queries with filters
        $search = $request->input('search');
        $filterStatus = $request->input('status');
        $filterUkuran = $request->input('ukuran');
        $filterTipe = $request->input('tipe_kontainer');

        // Query stock_kontainers for this gudang
        $stockKontainersQuery = StockKontainer::where('gudangs_id', $gudangId);
        
        if ($search) {
            $stockKontainersQuery->where(function($q) use ($search) {
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

        // Query kontainers for this gudang
        $kontainersQuery = Kontainer::where('gudangs_id', $gudangId);
        
        if ($search) {
            $kontainersQuery->where(function($q) use ($search) {
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
        $totalStockKontainers = StockKontainer::where('gudangs_id', $gudangId)->count();
        $totalKontainers = Kontainer::where('gudangs_id', $gudangId)->count();
        $totalAll = $totalStockKontainers + $totalKontainers;

        // Size breakdown
        $stockSizes = StockKontainer::where('gudangs_id', $gudangId)
            ->selectRaw("ukuran, COUNT(*) as total")
            ->groupBy('ukuran')
            ->pluck('total', 'ukuran')
            ->toArray();
            
        $kontainerSizes = Kontainer::where('gudangs_id', $gudangId)
            ->selectRaw("ukuran, COUNT(*) as total")
            ->groupBy('ukuran')
            ->pluck('total', 'ukuran')
            ->toArray();

        // Fetch supirs for the modal
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nama_panggilan']);

        // Fetch pricelists for Harga OB logic
        $pricelists = MasterPricelistOb::all();

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
            'nomor_kontainer' => 'required|string',
            'ukuran' => 'required|string',
            'nama_supir' => 'required|string',
            'pricelist_id' => 'required|exists:master_pricelist_ob,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'gudang_tujuan_id' => 'required|exists:gudangs,id',
            'source' => 'required|in:stock,kontainer',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $gudangAsal = Gudang::find($validated['gudang_id']);
            $gudangTujuan = Gudang::find($validated['gudang_tujuan_id']);
            $pricelist = MasterPricelistOb::find($validated['pricelist_id']);

            $tagihan = new TagihanOb();
            $tagihan->kapal = 'ANTAR GUDANG';
            $tagihan->voyage = 'ANTAR GUDANG';
            $tagihan->kegiatan = 'ANTAR GUDANG';
            $tagihan->nomor_kontainer = $validated['nomor_kontainer'];
            $tagihan->size_kontainer = $validated['ukuran'];
            $tagihan->nama_supir = $validated['nama_supir'];
            $tagihan->status_kontainer = $pricelist->status_kontainer;
            $tagihan->barang = 'KOSONGAN / ISI (ANTAR GUDANG)';
            $tagihan->keterangan = $validated['keterangan'] 
                ?? ('Antar Gudang: ' . ($gudangAsal->nama_gudang ?? '-') . ' → ' . ($gudangTujuan->nama_gudang ?? '-'));
            $tagihan->created_by = Auth::id();

            // Gunakan harga langsung dari pricelist yang dipilih
            $tagihan->biaya = $pricelist->biaya;
            
            $tagihan->save();

            // Update gudang_id pada kontainer terkait (pindahkan ke gudang tujuan)
            if ($validated['source'] === 'stock') {
                StockKontainer::where('gudangs_id', $validated['gudang_id'])
                    ->where(function($q) use ($validated) {
                        $q->where('nomor_seri_gabungan', $validated['nomor_kontainer'])
                          ->orWhere(DB::raw("CONCAT(awalan_kontainer, nomor_seri_kontainer)"), $validated['nomor_kontainer']);
                    })
                    ->update(['gudangs_id' => $validated['gudang_tujuan_id']]);
            } else {
                Kontainer::where('gudangs_id', $validated['gudang_id'])
                    ->where(function($q) use ($validated) {
                        $q->where('nomor_seri_gabungan', $validated['nomor_kontainer'])
                          ->orWhere(DB::raw("CONCAT(awalan_kontainer, nomor_seri_kontainer)"), $validated['nomor_kontainer']);
                    })
                    ->update(['gudangs_id' => $validated['gudang_tujuan_id']]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Tagihan OB Antar Gudang berhasil dibuat. Kontainer ' . $validated['nomor_kontainer'] . ' dipindahkan ke ' . ($gudangTujuan->nama_gudang ?? 'gudang tujuan') . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }
}
