<?php

namespace App\Http\Controllers;

use App\Models\StockBan;
use App\Models\Mobil;
use App\Models\NamaStockBan;
use App\Models\MerkBan;
use App\Models\Gudang;
use App\Models\StockRingVelg;
use App\Models\StockVelg;
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
        // Separate Ban Dalam, Ban Perut, and Lock Kontainer
        $stockBanDalamsOriginal = \App\Models\StockBanDalam::with('namaStockBan')->latest()->get();
        $stockBanDalams = $stockBanDalamsOriginal->filter(function($item) {
            return $item->namaStockBan && stripos($item->namaStockBan->nama, 'ban dalam') !== false;
        });
        $stockBanPeruts = $stockBanDalamsOriginal->filter(function($item) {
             return $item->namaStockBan && stripos($item->namaStockBan->nama, 'ban perut') !== false;
        });
        $stockLockKontainers = $stockBanDalamsOriginal->filter(function($item) {
             return $item->namaStockBan && stripos($item->namaStockBan->nama, 'lock kontainer') !== false;
        });
        
        $stockRingVelgs = StockRingVelg::with('namaStockBan')->latest()->get();
        $stockVelgs = StockVelg::with('namaStockBan')->latest()->get();

        $mobils = Mobil::orderBy('nomor_polisi')->get();
        // Assuming receivers are employees/karyawans
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        $nextInvoice = \App\Models\StockBan::generateNextInvoice();
        $pricelistKanisirBans = \App\Models\MasterPricelistKanisirBan::whereIn('status', ['active', 'aktif'])
            ->orderBy('vendor')
            ->get();

        return view('stock-ban.index', compact('stockBans', 'stockBanDalams', 'stockBanPeruts', 'stockLockKontainers', 'stockRingVelgs', 'stockVelgs', 'mobils', 'karyawans', 'nextInvoice', 'pricelistKanisirBans'));
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
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        $nextInvoice = StockBan::generateNextInvoice(); // Using the same generator for now
        return view('stock-ban.create', compact('mobils', 'namaStockBans', 'merkBans', 'gudangs', 'karyawans', 'nextInvoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // First check if it's Ban Dalam or Ban Perut or Lock Kontainer
        $namaStockBan = NamaStockBan::find($request->nama_stock_ban_id);
        
        // Check for Ring Velg
        $isRingVelg = $namaStockBan && stripos($namaStockBan->nama, 'ring velg') !== false;

        if ($isRingVelg) {
             $request->validate([
                'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
                'qty' => 'required|integer|min:0',
                'harga_beli' => 'nullable|numeric|min:0',
                'ukuran' => 'nullable|string|max:255',
                'tanggal_masuk' => 'required|date',
                'lokasi' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
                'nomor_bukti' => 'nullable|string|max:255',
                'type' => 'nullable|string',
            ]);

            $type = $request->filled('type') ? $request->type : 'pcs';

             // Check for existing record to increment
            $existingStock = StockRingVelg::where('nama_stock_ban_id', $request->nama_stock_ban_id)
                ->where('ukuran', $request->ukuran)
                ->where('lokasi', $request->lokasi)
                ->where('type', $type)
                ->first();

            if ($existingStock) {
                // Increment qty
                $existingStock->increment('qty', $request->qty);
                $existingStock->update([
                    'harga_beli' => $request->harga_beli,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'nomor_bukti' => $request->nomor_bukti ?? $existingStock->nomor_bukti,
                    'keterangan' => $request->keterangan ?? $existingStock->keterangan,
                ]);
            } else {
                StockRingVelg::create([
                    'nama_stock_ban_id' => $request->nama_stock_ban_id,
                    'nomor_bukti' => $request->nomor_bukti,
                    'ukuran' => $request->ukuran,
                    'type' => $type,
                    'qty' => $request->qty,
                    'harga_beli' => $request->harga_beli,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'lokasi' => $request->lokasi,
                    'keterangan' => $request->keterangan,
                ]);
            }

            return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ring Velg berhasil ditambahkan');
        }

        // Check for Velg (Explicitly Velg, not Ring Velg which is handled above)
        $isVelg = $namaStockBan && stripos($namaStockBan->nama, 'velg') !== false;

        if ($isVelg) {
            $request->validate([
                'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
                'qty' => 'required|integer|min:0',
                'harga_beli' => 'nullable|numeric|min:0',
                'ukuran' => 'nullable|string|max:255',
                'tanggal_masuk' => 'required|date',
                'lokasi' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
                'nomor_bukti' => 'nullable|string|max:255',
                'type' => 'nullable|string',
            ]);

            $type = $request->filled('type') ? $request->type : 'pcs';

            $existingStock = StockVelg::where('nama_stock_ban_id', $request->nama_stock_ban_id)
                ->where('ukuran', $request->ukuran)
                ->where('lokasi', $request->lokasi)
                ->where('type', $type)
                ->first();

            if ($existingStock) {
                $existingStock->increment('qty', $request->qty);
                $existingStock->update([
                    'harga_beli' => $request->harga_beli,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'nomor_bukti' => $request->nomor_bukti ?? $existingStock->nomor_bukti,
                    'keterangan' => $request->keterangan ?? $existingStock->keterangan,
                ]);
            } else {
                StockVelg::create([
                    'nama_stock_ban_id' => $request->nama_stock_ban_id,
                    'nomor_bukti' => $request->nomor_bukti,
                    'ukuran' => $request->ukuran,
                    'type' => $type,
                    'qty' => $request->qty,
                    'harga_beli' => $request->harga_beli,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'lokasi' => $request->lokasi,
                    'keterangan' => $request->keterangan,
                ]);
            }

            return redirect()->route('stock-ban.index')->with('success', 'Data Stock Velg berhasil ditambahkan');
        }

        $isBulkItem = $namaStockBan && (stripos($namaStockBan->nama, 'ban dalam') !== false || stripos($namaStockBan->nama, 'ban perut') !== false || stripos($namaStockBan->nama, 'lock kontainer') !== false);

        if ($isBulkItem) {
             $request->validate([
                'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
                'qty' => 'required|integer|min:0',
                'harga_beli' => 'nullable|numeric|min:0',
                'ukuran' => 'nullable|string|max:255',
                'tanggal_masuk' => 'required|date',
                'lokasi' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
                'nomor_bukti' => 'nullable|string|max:255',
                // For Ban Perut/Lock Kontainer, allow 'type' input if provided, otherwise default to 'pcs'
                'type' => 'nullable|string|in:pcs,set', // Add more if needed, or remove validation if dynamic
            ]);

            // Determine type: if provided in request use it, else default to 'pcs'
            // For Ban Dalam code was forcing 'pcs'. Let's keep 'pcs' default but allow override if sent.
            // However, previous code HARDCODED 'type' => 'pcs' in create/update.
            // User requested "input type" for Ban Perut.
            $type = $request->filled('type') ? $request->type : 'pcs';

            // Check for existing record to increment
            $existingStock = \App\Models\StockBanDalam::where('nama_stock_ban_id', $request->nama_stock_ban_id)
                ->where('ukuran', $request->ukuran)
                ->where('lokasi', $request->lokasi)
                ->where('type', $type)
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
                    'type' => $type,
                    'qty' => $request->qty,
                    'harga_beli' => $request->harga_beli,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'lokasi' => $request->lokasi,
                    'keterangan' => $request->keterangan,
                ]);
            }

            return redirect()->route('stock-ban.index')->with('success', 'Data Stock berhasil ditambahkan');
        }

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'nullable|unique:stock_bans,nomor_seri',
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required|in:afkir,asli,kaleng,kanisir,karung,liter,pail,pcs',

            'harga_beli' => 'nullable|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:karyawans,id',
            'status_ban_luar' => 'nullable|string',
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
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        return view('stock-ban.edit', compact('stockBan', 'mobils', 'namaStockBans', 'merkBans', 'gudangs', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'nullable|unique:stock_bans,nomor_seri,' . $stockBan->id,
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required|in:afkir,asli,kaleng,kanisir,karung,liter,pail,pcs',

            'harga_beli' => 'nullable|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:karyawans,id',
            'status_ban_luar' => 'nullable|string',
        ]);

        $data = $request->all();

        // Handle merk_id from dropdown
        if ($request->filled('merk_id')) {
            $merkBan = MerkBan::find($request->merk_id);
            if ($merkBan) {
                $data['merk'] = $merkBan->nama;
            }
        }

        $stockBan->update($data);

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

    /**
     * Store the usage of Lock Kontainer/Stock Ban Biasa (Pakai Ban).
     */
    public function storeUsage(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        $request->validate([
            'mobil_id' => 'required|exists:mobils,id',
            'penerima_id' => 'required|exists:karyawans,id',
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $stockBan->update([
            'status' => 'Terpakai',
            'mobil_id' => $request->mobil_id,
            'penerima_id' => $request->penerima_id,
            'tanggal_keluar' => $request->tanggal_keluar,
            'keterangan' => $request->keterangan ? ($stockBan->keterangan . "\n" . "[Pemakaian: " . $request->keterangan . "]") : $stockBan->keterangan,
        ]);

        return redirect()->route('stock-ban.index')->with('success', 'Ban berhasil digunakan dan status diperbarui.');
    }

    /**
     * Update the condition of the ban to 'kanisir'.
     */
    public function masak($id)
    {
        $stockBan = StockBan::findOrFail($id);

        $stockBan->update([
            'kondisi' => 'kanisir',
            'status_masak' => 'sudah',
            'jumlah_masak' => $stockBan->jumlah_masak + 1
        ]);

        return redirect()->route('stock-ban.index')->with('success', 'Ban berhasil dimasak menjadi Kanisir.');
    }

    /**
     * Update the condition of multiple bans to 'kanisir'.
     */
    public function bulkMasak(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:stock_bans,id',
            'nomor_invoice' => 'nullable|string|max:255',
            'tanggal_masuk_kanisir' => 'required|date',
            'vendor' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $bans = StockBan::whereIn('id', $request->ids)
            ->where('status', 'Stok')
            ->where('kondisi', '!=', 'afkir')
            ->get();

        $count = 0;

        foreach ($bans as $ban) {
            $ban->kondisi = 'kanisir';
            $ban->status_masak = 'sudah';
            $ban->jumlah_masak = ($ban->jumlah_masak ?? 0) + 1;
            $ban->nomor_bukti = $request->nomor_invoice;
            $ban->tanggal_masuk = $request->tanggal_masuk_kanisir; // Update date to kanisir date
            $ban->harga_beli = $request->harga; // Update price/cost
            
            // Append vendor info to keterangan
            $vendorNote = "[Masak Kanisir] Vendor: " . $request->vendor . ", Tgl: " . date('d-m-Y', strtotime($request->tanggal_masuk_kanisir));
            if ($ban->keterangan) {
                 $ban->keterangan .= "\n" . $vendorNote;
            } else {
                 $ban->keterangan = $vendorNote;
            }

            $ban->save();
            $count++;
        }

        return redirect()->route('stock-ban.index')->with('success', $count . ' Ban berhasil dimasak menjadi Kanisir.');
    }
}

