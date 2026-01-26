<?php

namespace App\Http\Controllers;

use App\Models\StockBan;
use App\Models\Mobil;
use App\Models\NamaStockBan;
use App\Models\MerkBan;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockBans = StockBan::with('mobil')->latest()->get();
        $stockBanDalams = \App\Models\StockBanDalam::with('namaStockBan')->latest()->get();
        return view('stock-ban.index', compact('stockBans', 'stockBanDalams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $namaStockBans = NamaStockBan::where('status', 'active')->orderBy('nama')->get();
        $merkBans = MerkBan::orderBy('nama')->get();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('stock-ban.create', compact('mobils', 'namaStockBans', 'merkBans', 'gudangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // First check if it's Ban Dalam
        $namaStockBan = NamaStockBan::find($request->nama_stock_ban_id);
        $isBanDalam = $namaStockBan && stripos($namaStockBan->nama, 'ban dalam') !== false;

        if ($isBanDalam) {
             $request->validate([
                'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
                'qty' => 'required|integer|min:0',
                'harga_beli' => 'required|numeric|min:0',
                'ukuran' => 'nullable|string|max:255',
                'tanggal_masuk' => 'required|date',
                'lokasi' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'nomor_bukti' => 'nullable|string|max:255',
                // 'status' is not in stock_ban_dalams table based on migration, assuming handled or not needed
            ]);


            // Check for existing record to increment
            $existingStock = \App\Models\StockBanDalam::where('nama_stock_ban_id', $request->nama_stock_ban_id)
                ->where('ukuran', $request->ukuran)
                ->where('lokasi', $request->lokasi)
                ->where('type', 'pcs')
                ->first();

            if ($existingStock) {
                // Increment qty
                $existingStock->increment('qty', $request->qty);
                // Optionally update last price or average it. Here we update to latest price/date
                $existingStock->update([
                    'harga_beli' => $request->harga_beli,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'nomor_bukti' => $request->nomor_bukti ?? $existingStock->nomor_bukti,
                    'keterangan' => $request->keterangan ?? $existingStock->keterangan,
                ]);
            } else {
                \App\Models\StockBanDalam::create([
                    'nama_stock_ban_id' => $request->nama_stock_ban_id,
                    'nomor_bukti' => $request->nomor_bukti,
                    'ukuran' => $request->ukuran,
                    'type' => 'pcs', // Force type to pcs as requested "dropdown pcs hanya berisi 'pcs'"
                    'qty' => $request->qty,
                    'harga_beli' => $request->harga_beli,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'lokasi' => $request->lokasi,
                    'keterangan' => $request->keterangan,
                ]);
            }

            return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban Dalam berhasil ditambahkan');
        }

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'required|unique:stock_bans,nomor_seri',
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required|in:afkir,asli,kaleng,kanisir,karung,liter,pail,pcs',

            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        // Handle merk_id from dropdown
        if ($request->filled('merk_id')) {
            $merkBan = MerkBan::find($request->merk_id);
            if ($merkBan) {
                $data['merk'] = $merkBan->nama;
            }
        }
        
        StockBan::create($data);

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stockBan = StockBan::findOrFail($id);
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $namaStockBans = NamaStockBan::where('status', 'active')->orderBy('nama')->get();
        $merkBans = MerkBan::orderBy('nama')->get();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        return view('stock-ban.edit', compact('stockBan', 'mobils', 'namaStockBans', 'merkBans', 'gudangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'required|unique:stock_bans,nomor_seri,' . $stockBan->id,
            'merk' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required|in:afkir,asli,kaleng,kanisir,karung,liter,pail,pcs',

            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
        ]);

        $stockBan->update($request->all());

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stockBan = StockBan::findOrFail($id);
        $stockBan->delete();

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil dihapus');
    }

    /**
     * Show the form for using Ban Dalam stock.
     */
    public function useBanDalam($id)
    {
        $stockBanDalam = \App\Models\StockBanDalam::findOrFail($id);
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        return view('stock-ban.use-ban-dalam', compact('stockBanDalam', 'mobils'));
    }

    /**
     * Store the usage of Ban Dalam.
     */
    public function storeUsageBanDalam(Request $request, $id)
    {
        $stockBanDalam = \App\Models\StockBanDalam::findOrFail($id);

        $request->validate([
            'mobil_id' => 'required|exists:mobils,id',
            'qty' => 'required|integer|min:1|max:' . $stockBanDalam->qty,
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $stockBanDalam) {
            // Create usage record
            \App\Models\StockBanDalamUsage::create([
                'stock_ban_dalam_id' => $stockBanDalam->id,
                'mobil_id' => $request->mobil_id,
                'qty' => $request->qty,
                'tanggal_keluar' => $request->tanggal_keluar,
                'keterangan' => $request->keterangan,
            ]);

            // Decrement stock
            $stockBanDalam->decrement('qty', $request->qty);
        });

        return redirect()->route('stock-ban.index')->with('success', 'Penggunaan Ban Dalam berhasil dicatat');
    }

    /**
     * Show details of Ban Dalam including usage history.
     */
    public function showBanDalam($id)
    {
        $stockBanDalam = \App\Models\StockBanDalam::with(['namaStockBan', 'usages.mobil'])->findOrFail($id);
        return view('stock-ban.show-ban-dalam', compact('stockBanDalam'));
    }
}

