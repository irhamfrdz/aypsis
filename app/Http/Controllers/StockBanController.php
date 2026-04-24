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
use App\Models\InvoiceKanisirBan;
use App\Models\InvoiceKanisirBanItem;

class StockBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockBans = StockBan::with('mobil')->latest()->get();
        $stockBanLuarBatams = \App\Models\StockBanLuarBatam::with('mobil')->latest()->get();
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
        
        $stockLainLains = $stockBanDalamsOriginal->filter(function($item) {
             return $item->namaStockBan && (
                stripos($item->namaStockBan->nama, 'cat') !== false || 
                stripos($item->namaStockBan->nama, 'majun') !== false ||
                (
                    stripos($item->namaStockBan->nama, 'ban dalam') === false &&
                    stripos($item->namaStockBan->nama, 'ban perut') === false &&
                    stripos($item->namaStockBan->nama, 'lock kontainer') === false
                )
             );
        });
        
        $stockRingVelgs = StockRingVelg::with('namaStockBan')->latest()->get();
        $stockVelgs = StockVelg::with('namaStockBan')->latest()->get();

        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $alatBerats = \App\Models\AlatBerat::orderBy('nama')->get();
        // Assuming receivers are employees/karyawans
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        $nextInvoice = \App\Models\StockBan::generateNextInvoice();
        $pricelistKanisirBans = \App\Models\MasterPricelistKanisirBan::whereIn('status', ['active', 'aktif'])
            ->orderBy('vendor')
            ->get();
        $kapals = \App\Models\MasterKapal::aktif()->orderBy('nama_kapal')->get();
        $masterGudangBans = \App\Models\MasterGudangBan::where('status', 'aktif')->orderBy('nama_gudang')->get();
        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();

        return view('stock-ban.index', compact('stockBans', 'stockBanLuarBatams', 'stockBanDalams', 'stockBanPeruts', 'stockLockKontainers', 'stockLainLains', 'stockRingVelgs', 'stockVelgs', 'mobils', 'alatBerats', 'karyawans', 'nextInvoice', 'pricelistKanisirBans', 'kapals', 'masterGudangBans', 'gudangs'));
    }

    /**
     * Display input harian view for Stock Ban and Stock Ban Luar Batam.
     */
    public function inputHarian(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        $stockBans = StockBan::with(['mobil', 'alatBerat', 'penerima', 'kapal', 'namaStockBan', 'createdBy'])
            ->whereDate('created_at', $date)
            ->latest()
            ->get();
            
        $stockBanLuarBatams = \App\Models\StockBanLuarBatam::with(['mobil', 'alatBerat', 'penerima', 'kapal', 'namaStockBan', 'createdBy'])
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        return view('stock-ban.input-harian', compact('stockBans', 'stockBanLuarBatams', 'date'));
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
        $masterGudangBans = \App\Models\MasterGudangBan::where('status', 'aktif')->orderBy('nama_gudang')->get();
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        $nextInvoice = StockBan::generateNextInvoice(); // Using the same generator for now
        return view('stock-ban.create', compact('mobils', 'namaStockBans', 'merkBans', 'gudangs', 'masterGudangBans', 'karyawans', 'nextInvoice'));
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
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

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
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            return redirect()->route('stock-ban.index')->with('success', 'Data Stock Velg berhasil ditambahkan');
        }

        $isBulkItem = $namaStockBan && (stripos($namaStockBan->nama, 'ban dalam') !== false || stripos($namaStockBan->nama, 'ban perut') !== false || stripos($namaStockBan->nama, 'lock kontainer') !== false || stripos($namaStockBan->nama, 'cat') !== false || stripos($namaStockBan->nama, 'majun') !== false || stripos($namaStockBan->nama, 'thinner') !== false);

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
                'type' => 'nullable|string', // Wide variety of types: pcs, set, liter, pail, etc.
            ]);

            // Determine type: if provided in request use it, else default to 'pcs'
            // For Ban Dalam code was forcing 'pcs'. Let's keep 'pcs' default but allow override if sent.
            // However, previous code HARDCODED 'type' => 'pcs' in create/update.
            // User requested "input type" for Ban Perut.
            $type = $request->filled('type') ? $request->type : 'pcs';

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
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            return redirect()->route('stock-ban.index')->with('success', 'Data Stock berhasil ditambahkan')->with('active_tab', 'tab-barang-lainnya');
        }

        if ($request->filled('no_serial_checkbox')) {
            $request->merge(['nomor_seri' => 'Tidak Ada No Seri - ' . strtoupper(uniqid())]);
        }

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'nullable|unique:stock_bans,nomor_seri',
            'nomor_faktur' => 'nullable|string|max:255',
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required|in:afkir,asli,kaleng,kanisir,karung,liter,pail,pcs,rusak',

            'harga_beli' => 'nullable|numeric|min:0',
            'tempat_beli' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:karyawans,id',
            'status_ban_luar' => 'nullable|string',
            'tanggal_digunakan' => 'nullable|date',
        ]);

        $data = $request->all();

        // Handle merk_id from dropdown
        if ($request->filled('merk_id')) {
            $merkBan = MerkBan::find($request->merk_id);
            if ($merkBan) {
                $data['merk'] = $merkBan->nama;
            }
        }

        // Auto set status to 'Rusak' if kondisi is 'afkir' or 'rusak'
        if ($request->kondisi === 'afkir' || $request->kondisi === 'rusak') {
            $data['status'] = 'Rusak';
        }
        
        StockBan::create($data);

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stockBan = StockBan::with(['mobil', 'alatBerat', 'penerima', 'kapal'])->findOrFail($id);
        return view('stock-ban.show', compact('stockBan'));
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
     * Show the form for editing other stock items.
     */
    public function editStockLain($type, $id)
    {
        $model = $this->getModelByType($type);
        $item = $model::with('namaStockBan')->findOrFail($id);
        
        $namaStockBans = NamaStockBan::where('status', 'active')->orderBy('nama')->get();
        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
        
        return view('stock-ban.edit-stock-lain', compact('item', 'type', 'namaStockBans', 'gudangs'));
    }

    /**
     * Update the specified other stock item in storage.
     */
    public function updateStockLain(Request $request, $type, $id)
    {
        $model = $this->getModelByType($type);
        $item = $model::findOrFail($id);
        
        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'qty' => 'required|integer|min:0',
            'harga_beli' => 'nullable|numeric|min:0',
            'ukuran' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:50',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $item->update([
            'nama_stock_ban_id' => $request->nama_stock_ban_id,
            'qty' => $request->qty,
            'harga_beli' => $request->harga_beli,
            'ukuran' => $request->ukuran,
            'type' => $request->type ?? $item->type,
            'tanggal_masuk' => $request->tanggal_masuk,
            'lokasi' => $request->lokasi,
            'keterangan' => $request->keterangan,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('stock-ban.index', ['tab' => 'barang-lainnya'])->with('success', 'Data stock ' . ucwords(str_replace('-', ' ', $type)) . ' berhasil diperbarui');
    }

    /**
     * Remove the specified other stock item from storage.
     */
    public function destroyStockLain($type, $id)
    {
        $model = $this->getModelByType($type);
        $item = $model::findOrFail($id);
        $item->delete();

        return redirect()->route('stock-ban.index', ['tab' => 'barang-lainnya'])->with('success', 'Data stock ' . ucwords(str_replace('-', ' ', $type)) . ' berhasil dihapus');
    }

    /**
     * Helper to get model class by type string.
     */
    private function getModelByType($type)
    {
        switch ($type) {
            case 'ban-dalam':
            case 'ban-perut':
            case 'lock-kontainer':
            case 'lainnya':
            case 'cat':
            case 'majun':
            case 'thinner':
                return \App\Models\StockBanDalam::class;
            case 'ring-velg':
                return \App\Models\StockRingVelg::class;
            case 'velg':
                return \App\Models\StockVelg::class;
            default:
                abort(404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        if ($request->filled('no_serial_checkbox')) {
            if (!str_starts_with($stockBan->nomor_seri ?? '', 'Tidak Ada No Seri')) {
                $request->merge(['nomor_seri' => 'Tidak Ada No Seri - ' . strtoupper(uniqid())]);
            } else {
                $request->merge(['nomor_seri' => $stockBan->nomor_seri]);
            }
        }

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'nullable|unique:stock_bans,nomor_seri,' . $stockBan->id,
            'nomor_faktur' => 'nullable|string|max:255',
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required|in:afkir,asli,kaleng,kanisir,karung,liter,pail,pcs,rusak',

            'harga_beli' => 'nullable|numeric|min:0',
            'tempat_beli' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:karyawans,id',
            'status_ban_luar' => 'nullable|string',
            'tanggal_digunakan' => 'nullable|date',
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

        // Informative check: only Stok can be used
        if ($stockBan->status !== 'Stok') {
            return redirect()->back()->with('error', 'Gagal: Ban ini sedang dalam status "' . $stockBan->status . '" dan tidak bisa dikonfigurasi ulang untuk pemakaian.')->withInput();
        }

        $selectionId = $request->mobil_id;
        $isAlatBerat = false;
        
        // Handle Alat Berat prefix
        if ($selectionId && str_starts_with($selectionId, 'alat_berat_')) {
             $isAlatBerat = true;
             $selectionId = str_replace('alat_berat_', '', $selectionId);
        }

        // Add to request for easier validation
        $request->merge(['processed_unit_id' => $selectionId]);

        $request->validate([
            'mobil_id' => 'required',
            'processed_unit_id' => $isAlatBerat ? 'exists:alat_berats,id' : 'exists:mobils,id',
            'penerima_id' => 'required|exists:karyawans,id',
            'tanggal_keluar' => 'required|date',
            'tanggal_digunakan' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ], [
            'mobil_id.required' => 'Wajib memilih Mobil atau Alat Berat.',
            'processed_unit_id.exists' => $isAlatBerat ? 'Alat Berat tidak valid.' : 'Mobil tidak valid.',
            'penerima_id.required' => 'Wajib memilih Penerima (Supir/Kenek).',
            'penerima_id.exists' => 'Penerima tidak valid.',
            'tanggal_keluar.required' => 'Tanggal pasang harus diisi.',
        ]);

        $updateData = [
            'status' => 'Terpakai',
            'penerima_id' => $request->penerima_id,
            'tanggal_keluar' => $request->tanggal_keluar,
            'tanggal_digunakan' => $request->tanggal_digunakan ?? $request->tanggal_keluar,
            'keterangan' => $request->keterangan ? ($stockBan->keterangan . "\n" . "[Pemakaian: " . $request->keterangan . "]") : $stockBan->keterangan,
        ];

        if ($isAlatBerat) {
            $updateData['alat_berat_id'] = $selectionId;
            $updateData['mobil_id'] = null;
        } else {
            $updateData['mobil_id'] = $selectionId;
            $updateData['alat_berat_id'] = null;
        }

        $stockBan->update($updateData);

        $unitName = $isAlatBerat ? 'alat berat' : 'mobil';
        return redirect()->route('stock-ban.index')->with('success', 'Ban dengan nomor seri ' . ($stockBan->nomor_seri ?? '-') . ' berhasil dipasang pada ' . $unitName . '.');
    }

    /**
     * Send ban to ship.
     */
    public function kirim(Request $request, $id)
    {
        return $this->processKirim($request, $id, 'Batam');
    }

    /**
     * Send ban to Tanjung Pinang.
     */
    public function kirimTanjungPinang(Request $request, $id)
    {
        return $this->processKirim($request, $id, 'Tanjung Pinang');
    }

    /**
     * Process sending ban to a destination.
     */
    private function processKirim(Request $request, $id, $destination)
    {
        $stockBan = StockBan::findOrFail($id);

        // Informative check: only Stok can be sent
        if ($stockBan->status !== 'Stok') {
            return redirect()->back()->with('error', 'Gagal: Ban ini sedang dalam status "' . $stockBan->status . '" dan tidak bisa dikirim.')->withInput();
        }

        $request->validate([
            'penerima_id' => 'required|exists:karyawans,id',
            'kapal_id' => 'required|exists:master_kapals,id',
            'tanggal_kirim' => 'required|date',
            'keterangan' => 'nullable|string',
        ], [
            'penerima_id.required' => 'Wajib memilih Penerima.',
            'penerima_id.exists' => 'Penerima tidak valid.',
            'kapal_id.required' => 'Wajib memilih Kapal.',
            'kapal_id.exists' => 'Kapal tidak valid.',
            'tanggal_kirim.required' => 'Tanggal kirim harus diisi.',
        ]);

        $status = "Dikirim Ke " . $destination;
        $keteranganNote = "[Kirim ke " . $destination . ": " . ($request->keterangan ?? '-') . "]";

        $stockBan->update([
            'status' => $status,
            'penerima_id' => $request->penerima_id,
            'kapal_id' => $request->kapal_id,
            'tanggal_kirim' => $request->tanggal_kirim,
            'keterangan' => $stockBan->keterangan ? ($stockBan->keterangan . "\n" . $keteranganNote) : $keteranganNote,
        ]);

        $kapalName = \App\Models\MasterKapal::find($request->kapal_id)->nama_kapal ?? '-';
        return redirect()->route('stock-ban.index')->with('success', 'Ban dengan nomor seri ' . ($stockBan->nomor_seri ?? '-') . ' berhasil dikirim ke ' . $destination . ' (' . $kapalName . ').');
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
            'nomor_faktur_vendor' => 'nullable|string|max:255',
            'tanggal_masuk_kanisir' => 'required|date',
            'vendor' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $bans = StockBan::whereIn('id', $request->ids)
            ->where('status', 'Stok')
            ->where('kondisi', '!=', 'afkir')
            ->get();
        
        if ($bans->isEmpty()) {
             return redirect()->back()->with('error', 'Tidak ada ban yang valid untuk dimasak/kanisir.');
        }

        DB::transaction(function () use ($request, $bans) {
             // Create Invoice Header
            $invoice = InvoiceKanisirBan::create([
                'nomor_invoice' => $request->nomor_invoice ?? 'INV-KANISIR-' . time(),
                'nomor_faktur' => $request->nomor_faktur_vendor, // Save vendor invoice number
                'tanggal_invoice' => $request->tanggal_masuk_kanisir,
                'vendor' => $request->vendor,
                'total_biaya' => $request->harga * $bans->count(),
                'jumlah_ban' => $bans->count(),
                'keterangan' => 'Masak Kanisir manual',
                'status' => 'pending',
            ]);

            foreach ($bans as $ban) {
                // Create Invoice Item
                InvoiceKanisirBanItem::create([
                    'invoice_kanisir_ban_id' => $invoice->id,
                    'stock_ban_id' => $ban->id,
                    'harga' => $request->harga,
                ]);

                // Update Stock Ban
                $ban->kondisi = 'kanisir';
                $ban->status = 'Sedang Dimasak'; // Set status to Sedang Dimasak
                $ban->status_masak = 'sudah';
                $ban->jumlah_masak = ($ban->jumlah_masak ?? 0) + 1;
                $ban->nomor_bukti = $invoice->nomor_invoice;
                $ban->nomor_faktur = $request->nomor_faktur_vendor; // Update vendor invoice on the ban record as well
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
            }
        });

        // Use count from bans collection since we processed all valid ones
        return redirect()->route('stock-ban.index')->with('success', $bans->count() . ' Ban berhasil dimasak menjadi Kanisir. Invoice berhasil dibuat.');
    }

    /**
     * Return ban from mobil to warehouse.
     */
    public function returnToWarehouse(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        // Validate that the ban is currently in use
        if ($stockBan->status !== 'Terpakai') {
            return redirect()->route('stock-ban.index')->with('error', 'Ban ini tidak sedang terpakai, tidak bisa dikembalikan ke gudang.');
        }

        $request->validate([
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Get mobil info before clearing it
        $mobilPolisi = $stockBan->mobil ? $stockBan->mobil->nomor_polisi : '-';
        
        // Build return note
        $returnNote = "[Kembali ke Gudang] Dari mobil: " . $mobilPolisi . ", Tgl: " . date('d-m-Y');
        if ($request->filled('keterangan')) {
            $returnNote .= ", Ket: " . $request->keterangan;
        }

        // Update stock ban
        $stockBan->update([
            'status' => 'Stok',
            'mobil_id' => null,
            'lokasi' => $request->lokasi,
            'tanggal_keluar' => null,
            'keterangan' => $stockBan->keterangan ? ($stockBan->keterangan . "\n" . $returnNote) : $returnNote,
        ]);

        return redirect()->route('stock-ban.index')->with('success', 'Ban berhasil dikembalikan ke gudang.');
    }

    /**
     * Mark ban as finished cooking (return from masak).
     */
    public function returnFromMasak(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        if ($stockBan->status !== 'Sedang Dimasak') {
            return redirect()->route('stock-ban.index')->with('error', 'Ban ini tidak sedang dimasak.');
        }

        $request->validate([
            'lokasi' => 'required|string|max:255',
            'tanggal_kembali' => 'required|date',
        ]);

        $tanggalKembali = \Carbon\Carbon::parse($request->tanggal_kembali)->format('d-m-Y');

        $stockBan->update([
            'status' => 'Stok',
            'lokasi' => $request->lokasi,
            'tanggal_kembali' => $request->tanggal_kembali,
            'keterangan' => $stockBan->keterangan . "\n[Selesai Masak] Kembali ke stok di " . $request->lokasi . ", Tgl: " . $tanggalKembali,
        ]);

        return redirect()->route('stock-ban.index')->with('success', 'Ban selesai dimasak dan kembali ke stok.');
    }

    /**
     * Return ban to shop/vendor.
     */
    public function returnToShop(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        // Only stock bans can be returned to shop
        if ($stockBan->status !== 'Stok') {
            return redirect()->route('stock-ban.index')->with('error', 'Hanya ban dengan status "Stok" yang bisa dikembalikan ke toko.');
        }

        $request->validate([
            'tanggal_kembali' => 'required|date',
            'nama_toko' => 'nullable|string|max:255',
            'keterangan_kembali' => 'nullable|string',
        ]);

        $tanggalKembali = \Carbon\Carbon::parse($request->tanggal_kembali)->format('d-m-Y');

        $returnNote = "[Kembali ke Toko] ";
        if ($request->filled('nama_toko')) {
            $returnNote .= "Toko: " . $request->nama_toko . ", ";
        }
        $returnNote .= "Tgl: " . $tanggalKembali;
        
        if ($request->filled('keterangan_kembali')) {
            $returnNote .= ", Ket: " . $request->keterangan_kembali;
        }

        $updateData = [
            'status' => 'Dikembalikan',
            'tanggal_kembali' => $request->tanggal_kembali,
            'keterangan' => $stockBan->keterangan ? ($stockBan->keterangan . "\n" . $returnNote) : $returnNote,
            'mobil_id' => null,
            'alat_berat_id' => null,
            'penerima_id' => null,
            'kapal_id' => null,
            'tanggal_keluar' => null,
            'tanggal_kirim' => null,
        ];

        if ($request->filled('nama_toko')) {
            $updateData['tempat_beli'] = $request->nama_toko;
            $updateData['lokasi'] = $request->nama_toko;
        }

        $stockBan->update($updateData);

        return redirect()->route('stock-ban.index')->with('success', 'Ban berhasil dikembalikan ke toko.');
    }

    /**
     * Sell ban.
     */
    public function jual(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        if ($stockBan->status !== 'Stok' && $stockBan->status !== 'Rusak') {
            return redirect()->route('stock-ban.index')->with('error', 'Hanya ban dengan status "Stok" atau "Rusak" yang bisa dijual.');
        }

        $request->validate([
            'harga_jual' => 'required|numeric|min:0',
            'pembeli' => 'required|string|max:255',
            'tanggal_jual' => 'required|date',
            'keterangan_jual' => 'nullable|string',
        ]);

        $jualNote = "[Dijual] Pembeli: " . $request->pembeli . ", Harga: " . number_format($request->harga_jual, 0, ',', '.') . ", Tgl: " . date('d-m-Y', strtotime($request->tanggal_jual));
        if ($request->filled('keterangan_jual')) {
            $jualNote .= ", Ket: " . $request->keterangan_jual;
        }

        $stockBan->update([
            'status' => 'Dijual',
            'harga_jual' => $request->harga_jual,
            'pembeli' => $request->pembeli,
            'tanggal_jual' => $request->tanggal_jual,
            'keterangan' => $stockBan->keterangan ? ($stockBan->keterangan . "\n" . $jualNote) : $jualNote,
            'mobil_id' => null,
            'alat_berat_id' => null,
            'penerima_id' => null,
            'kapal_id' => null,
            'tanggal_keluar' => null,
            'tanggal_kirim' => null,
        ]);

        return redirect()->route('stock-ban.index')->with('success', 'Ban berhasil dijual.');
    }

    /**
     * Restore returned ban to stock.
     */
    public function restoreToStock(Request $request, $id)
    {
        $stockBan = StockBan::findOrFail($id);

        if ($stockBan->status !== 'Dikembalikan') {
            return redirect()->route('stock-ban.index')->with('error', 'Hanya ban dengan status "Dikembalikan" yang bisa dikembalikan ke stok.');
        }

        $request->validate([
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $restoreNote = "[Kembali ke Stok] Lokasi: " . $request->lokasi . ", Tgl: " . date('d-m-Y');
        if ($request->filled('keterangan')) {
            $restoreNote .= ", Ket: " . $request->keterangan;
        }

        $stockBan->update([
            'status' => 'Stok',
            'lokasi' => $request->lokasi,
            'tanggal_kembali' => null,
            'keterangan' => $stockBan->keterangan ? ($stockBan->keterangan . "\n" . $restoreNote) : $restoreNote,
        ]);

        return redirect()->route('stock-ban.index')->with('success', 'Ban berhasil dikembalikan ke stok.');
    }
    /**
     * Store stock usage for quantity-based items.
     */
    public function storeStockUsage(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'item_jenis' => 'required',
            'qty' => 'required|integer|min:1',
            'penerima_id' => 'required|exists:karyawans,id',
            'gudang_id' => 'nullable|exists:master_gudang_bans,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'tanggal_digunakan' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $itemId = $request->item_id;
        $jenis = $request->item_jenis;
        $qtyUsed = $request->qty;

        DB::beginTransaction();
        try {
            $item = null;
            $tableName = '';
            
            if (in_array($jenis, ['Ban Dalam', 'Ban Perut', 'Lock Kontainer', 'Cat', 'Majun', 'Thinner', 'Lainnya'])) {
                $item = \App\Models\StockBanDalam::findOrFail($itemId);
                $tableName = 'stock_ban_dalams';
            } elseif ($jenis == 'Ring Velg') {
                $item = \App\Models\StockRingVelg::findOrFail($itemId);
                $tableName = 'stock_ring_velgs';
            } elseif ($jenis == 'Velg') {
                $item = \App\Models\StockVelg::findOrFail($itemId);
                $tableName = 'stock_velgs';
            }

            if (!$item) {
                throw new \Exception('Jenis barang tidak dikenal.');
            }

            if ($item->qty < $qtyUsed) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }

            // Update qty
            $item->decrement('qty', $qtyUsed);

            // Record usage
            // We'll use StockBanDalamUsage as a general usage record for now
            // If it's not StockBanDalam, we store the ID in keterangan for traceability
            \App\Models\StockBanDalamUsage::create([
                'stock_ban_dalam_id' => ($tableName == 'stock_ban_dalams') ? $item->id : null,
                'qty' => $qtyUsed,
                'penerima_id' => $request->penerima_id,
                'gudang_id' => $request->gudang_id,
                'kapal_id' => $request->kapal_id,
                'tanggal_keluar' => $request->tanggal_digunakan ?? now(),
                'tanggal_digunakan' => $request->tanggal_digunakan ?? now(),
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
                'keterangan' => ($tableName != 'stock_ban_dalams') 
                    ? "[$jenis ID: $itemId] " . $request->keterangan 
                    : $request->keterangan,
            ]);

            DB::commit();
            return redirect()->route('stock-ban.index')->with('success', 'Pemakaian stok berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pemakaian: ' . $e->getMessage());
        }
    }
    /**
     * Show all movement history (Inbound and Outbound) for quantity-based items.
     */
    public function allUsageHistory()
    {
        // 1. Get Inbound (Creation of StockBanDalam, RingVelg, Velg)
        $inboundBan = \App\Models\StockBanDalam::with(['namaStockBan', 'createdBy'])->get()->map(function($item) {
            return (object)[
                'tanggal' => $item->tanggal_masuk,
                'created_at' => $item->created_at,
                'jenis_pergerakan' => 'MASUK',
                'nama' => ($item->namaStockBan->nama ?? 'Barang Lainnya'),
                'qty' => $item->qty_awal ?? $item->qty,
                'pelaku' => 'System / Purchase',
                'updater' => $item->createdBy->username ?? '-',
                'tujuan_penerima' => $item->lokasi ?? '-',
                'keterangan' => $item->keterangan ?? '-',
                'item_id' => $item->id,
                'source_table' => 'stock_ban_dalams',
                'original_id' => $item->id,
            ];
        });

        $inboundRing = \App\Models\StockRingVelg::with(['namaStockBan', 'createdBy'])->get()->map(function($item) {
            return (object)[
                'tanggal' => $item->tanggal_masuk,
                'created_at' => $item->created_at,
                'jenis_pergerakan' => 'MASUK',
                'nama' => 'Ring Velg: ' . ($item->namaStockBan->nama ?? $item->ukuran),
                'qty' => $item->qty,
                'pelaku' => 'System / Purchase',
                'updater' => $item->createdBy->username ?? '-',
                'tujuan_penerima' => $item->lokasi ?? '-',
                'keterangan' => $item->keterangan ?? '-',
                'item_id' => $item->id,
                'source_table' => 'stock_ring_velgs',
                'original_id' => $item->id,
            ];
        });

        $inboundVelg = \App\Models\StockVelg::with(['namaStockBan', 'createdBy'])->get()->map(function($item) {
            return (object)[
                'tanggal' => $item->tanggal_masuk,
                'created_at' => $item->created_at,
                'jenis_pergerakan' => 'MASUK',
                'nama' => 'Velg: ' . ($item->namaStockBan->nama ?? $item->ukuran),
                'qty' => $item->qty,
                'pelaku' => 'System / Purchase',
                'updater' => $item->createdBy->username ?? '-',
                'tujuan_penerima' => $item->lokasi ?? '-',
                'keterangan' => $item->keterangan ?? '-',
                'item_id' => $item->id,
                'source_table' => 'stock_velgs',
                'original_id' => $item->id,
            ];
        });

        $inbound = $inboundBan->concat($inboundRing)->concat($inboundVelg);

        // 2. Get Outbound (Usage)
        $outbound = \App\Models\StockBanDalamUsage::with(['stockBanDalam.namaStockBan', 'penerima', 'mobil', 'kapal', 'gudang', 'createdBy'])->get()->map(function($item) {
            $tujuan = '-';
            if ($item->mobil) $tujuan = $item->mobil->nomor_polisi;
            elseif ($item->kapal) $tujuan = $item->kapal->nama_kapal;
            elseif ($item->gudang) $tujuan = $item->gudang->nama_gudang;

            $nama = $item->stockBanDalam->namaStockBan->nama ?? 'Barang Lainnya';
            
            // If it's a generic reference (Velg/Ring Velg recorded in keterangan)
            if (!$item->stock_ban_dalam_id && preg_match('/\[(.*?) ID: (.*?)\]/', $item->keterangan, $matches)) {
                $nama = $matches[1] . " (Ref ID: " . $matches[2] . ")";
            }

            return (object)[
                'tanggal' => $item->tanggal_keluar,
                'created_at' => $item->created_at,
                'jenis_pergerakan' => 'KELUAR',
                'nama' => $nama,
                'qty' => $item->qty,
                'pelaku' => $item->penerima->nama_lengkap ?? '-',
                'updater' => $item->createdBy->username ?? '-',
                'tujuan_penerima' => $tujuan,
                'keterangan' => $item->keterangan ?? '-',
                'item_id' => $item->stock_ban_dalam_id ?? 'N/A',
                'source_table' => 'stock_ban_dalam_usages',
                'original_id' => $item->id,
            ];
        });

        // 3. Merge and Sort
        $history = $inbound->concat($outbound)->sortByDesc(function($item) {
            return $item->tanggal . $item->created_at;
        });
            
        return view('stock-ban.all-usage-history', compact('history'));
    }

    /**
     * Update history date for any quantity-based stock item.
     */
    public function updateHistoryDate(Request $request)
    {
        $request->validate([
            'source_table' => 'required',
            'original_id' => 'required',
            'new_date' => 'required|date',
            'field' => 'nullable|string', // Optional field name for tables with multiple dates
        ]);

        $table = $request->source_table;
        $id = $request->original_id;
        $newDate = $request->new_date;
        $field = $request->field;

        switch ($table) {
            case 'stock_ban_dalams':
            case 'stock_ring_velgs':
            case 'stock_velgs':
                DB::table($table)->where('id', $id)->update(['tanggal_masuk' => $newDate]);
                break;
            case 'stock_ban_dalam_usages':
                DB::table($table)->where('id', $id)->update([
                    'tanggal_keluar' => $newDate,
                    'tanggal_digunakan' => $newDate
                ]);
                break;
            case 'stock_bans':
                $updateField = $field ?? 'tanggal_masuk';
                // Security check for field name
                if (!in_array($updateField, ['tanggal_masuk', 'tanggal_keluar', 'tanggal_digunakan', 'tanggal_kirim', 'tanggal_jual'])) {
                    return response()->json(['success' => false, 'message' => 'Field tidak valid']);
                }
                DB::table($table)->where('id', $id)->update([$updateField => $newDate]);
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Tabel tidak valid']);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete a usage record and restore stock.
     */
    public function destroyUsageBanDalam($id)
    {
        $usage = \App\Models\StockBanDalamUsage::findOrFail($id);
        $stockBanDalam = \App\Models\StockBanDalam::findOrFail($usage->stock_ban_dalam_id);

        try {
            DB::transaction(function () use ($usage, $stockBanDalam) {
                // Restore stock
                $stockBanDalam->increment('qty', $usage->qty);
                // Delete usage record
                $usage->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Riwayat penggunaan berhasil dihapus dan stok telah dikembalikan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat: ' . $e->getMessage()
            ]);
        }
    }
}
