<?php

namespace App\Http\Controllers;

use App\Models\StockAmprahan;
use App\Models\MasterNamaBarangAmprahan;
use App\Models\MasterGudangAmprahan;
use App\Models\StockAmprahanUsage;
use App\Models\Karyawan;
use App\Models\Mobil;
use App\Models\AlatBerat;
use App\Models\MasterKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StockAmprahanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'updatedBy', 'usages.kendaraan', 'usages.truck', 'usages.buntut', 'usages.kapal', 'usages.alatBerat'])
            ->withSum('usages', 'jumlah')
            ->latest();
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('nomor_bukti', 'like', '%' . $search . '%')
                  ->orWhereHas('masterNamaBarangAmprahan', function($q) use ($search) {
                      $q->where('nama_barang', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('lokasi')) {
            $lokasi = $request->lokasi;
            if ($lokasi === 'LAINNYA') {
                $query->where(function($q) {
                    $q->whereNotIn('lokasi', ['KANTOR AYP JAKARTA', 'KANTOR AYP BATAM'])
                      ->orWhereNull('lokasi');
                });
            } else {
                $query->where('lokasi', $lokasi);
            }
        }
        
        $items = $query->paginate(20)->withQueryString();
            
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $kendaraans = Mobil::orderBy('nomor_polisi')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();

        // Stats for Cards
        $stats = [
            'total_qty' => StockAmprahan::sum('jumlah'),
            'total_jenis' => StockAmprahan::count(),
            'jakarta' => StockAmprahan::where('lokasi', 'KANTOR AYP JAKARTA')->sum('jumlah'),
            'batam' => StockAmprahan::where('lokasi', 'KANTOR AYP BATAM')->sum('jumlah'),
            'lainnya' => StockAmprahan::where(function($q) {
                $q->whereNotIn('lokasi', ['KANTOR AYP JAKARTA', 'KANTOR AYP BATAM'])
                  ->orWhereNull('lokasi');
            })->sum('jumlah'),
        ];

        return view('stock-amprahan.index', compact('items', 'karyawans', 'kendaraans', 'alatBerats', 'kapals', 'search', 'stats'));
    }

    public function create()
    {
        $masterItems = MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $gudangItems = MasterGudangAmprahan::where('status', 'active')->orderBy('nama_gudang')->get();
        
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $kendaraans = Mobil::orderBy('nomor_polisi')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();

        $mobils = $kendaraans;
        return view('stock-amprahan.create', compact('masterItems', 'gudangItems', 'karyawans', 'kendaraans', 'mobils', 'kapals', 'alatBerats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_bukti' => 'nullable|string|max:255',
            'tanggal_beli' => 'nullable|date',
            'type_amprahan' => 'required|in:Stock,Pemakaian,Perbaikan,Perlengkapan,Transportasi',
            'nama_barang' => 'required|string|max:255',
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'harga_satuan' => 'nullable|numeric|min:0',
            'adjustment' => 'nullable|numeric',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            
            // Langsung Pakai Fields
            'is_langsung_pakai' => 'nullable',
            'penerima_id' => 'nullable|required_if:is_langsung_pakai,1|exists:karyawans,id',
            'kendaraan_id' => 'nullable|exists:mobils,id',
            'truck_id' => 'nullable|exists:mobils,id',
            'buntut_id' => 'nullable|exists:mobils,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
            'lain_lain' => 'nullable|string|max:255',
            'tanggal_pengambilan' => 'nullable|required_if:is_langsung_pakai,1|date',
            'jumlah_pakai' => 'nullable|required_if:is_langsung_pakai,1|numeric|min:0',
            'keterangan_pakai' => 'nullable|required_if:is_langsung_pakai,1|string',
            'kilometer' => 'nullable|numeric|min:0',
        ]);

        $data['created_by'] = Auth::id();
        
        // Manual validation for jumlah_pakai if is_langsung_pakai
        if ($request->is_langsung_pakai == '1') {
            if ($request->jumlah_pakai > $request->jumlah) {
                return redirect()->back()->withErrors(['jumlah_pakai' => 'Jumlah pakai tidak boleh lebih besar dari jumlah stock.'])->withInput();
            }
        }

        $stockData = [
            'nomor_bukti' => $data['nomor_bukti'],
            'tanggal_beli' => $data['tanggal_beli'],
            'type_amprahan' => $data['type_amprahan'],
            'nama_barang' => $data['nama_barang'],
            'master_nama_barang_amprahan_id' => $data['master_nama_barang_amprahan_id'],
            'harga_satuan' => $data['harga_satuan'],
            'adjustment' => $data['adjustment'] ?? 0,
            'jumlah' => $data['jumlah'],
            'satuan' => $data['satuan'],
            'lokasi' => $data['lokasi'],
            'keterangan' => $data['keterangan'],
            'created_by' => $data['created_by'],
        ];

        // If langsung pakai, deduct from initial stock immediately
        if ($request->is_langsung_pakai == '1') {
            $stockData['jumlah'] -= $request->jumlah_pakai;
        }

        $stock = StockAmprahan::create($stockData);

        // Record usage if applicable
        if ($request->is_langsung_pakai == '1') {
            StockAmprahanUsage::create([
                'stock_amprahan_id' => $stock->id,
                'penerima_id' => $request->penerima_id,
                'kendaraan_id' => $request->kendaraan_id,
                'truck_id' => $request->truck_id,
                'buntut_id' => $request->buntut_id,
                'kapal_id' => $request->kapal_id,
                'alat_berat_id' => $request->alat_berat_id,
                'lain_lain' => $request->lain_lain,
                'jumlah' => $request->jumlah_pakai,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => $request->keterangan_pakai,
                'kilometer' => $request->kilometer,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil ditambahkan' . ($request->is_langsung_pakai == '1' ? ' dan langsung diproses pemakaiannya.' : '.'));
    }

    public function show($id)
    {
        $item = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('stock-amprahan.show', compact('item'));
    }

    public function edit($id)
    {
        $item = StockAmprahan::with(['usages' => fn($q) => $q->latest()])->findOrFail($id);
        $masterItems = MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $gudangItems = MasterGudangAmprahan::where('status', 'active')->orderBy('nama_gudang')->get();
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $kendaraans = Mobil::orderBy('nomor_polisi')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();

        return view('stock-amprahan.edit', compact('item', 'masterItems', 'gudangItems', 'karyawans', 'kendaraans', 'kapals', 'alatBerats'));
    }

    public function update(Request $request, $id)
    {
        $item = StockAmprahan::findOrFail($id);
        
        $data = $request->validate([
            'nomor_bukti' => 'nullable|string|max:255',
            'tanggal_beli' => 'nullable|date',
            'type_amprahan' => 'required|in:Stock,Pemakaian,Perbaikan,Perlengkapan,Transportasi',
            'nama_barang' => 'required|string|max:255',
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'harga_satuan' => 'nullable|numeric|min:0',
            'adjustment' => 'nullable|numeric',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'is_langsung_pakai' => 'nullable',
            'penerima_id' => 'nullable|required_if:is_langsung_pakai,1|exists:karyawans,id',
            'kendaraan_id' => 'nullable|exists:mobils,id',
            'truck_id' => 'nullable|exists:mobils,id',
            'buntut_id' => 'nullable|exists:mobils,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
            'lain_lain' => 'nullable|string|max:255',
            'tanggal_pengambilan' => 'nullable|required_if:is_langsung_pakai,1|date',
            'jumlah_pakai' => 'nullable|required_if:is_langsung_pakai,1|numeric|min:0',
            'keterangan_pakai' => 'nullable|required_if:is_langsung_pakai,1|string',
            'kilometer' => 'nullable|numeric|min:0',
        ]);

        $data['updated_by'] = Auth::id();

        // Manual validation for jumlah_pakai if is_langsung_pakai
        if ($request->is_langsung_pakai == '1') {
            if ($request->jumlah_pakai > $data['jumlah']) {
                return redirect()->back()->withErrors(['jumlah_pakai' => 'Jumlah pakai tidak boleh lebih besar dari jumlah stock.'])->withInput();
            }
        }

        $stockData = [
            'nomor_bukti' => $data['nomor_bukti'],
            'tanggal_beli' => $data['tanggal_beli'],
            'type_amprahan' => $data['type_amprahan'],
            'nama_barang' => $data['nama_barang'],
            'master_nama_barang_amprahan_id' => $data['master_nama_barang_amprahan_id'],
            'harga_satuan' => $data['harga_satuan'],
            'adjustment' => $data['adjustment'] ?? 0,
            'jumlah' => $data['jumlah'],
            'satuan' => $data['satuan'],
            'lokasi' => $data['lokasi'],
            'keterangan' => $data['keterangan'],
            'updated_by' => $data['updated_by'],
        ];

        // If langsung pakai, deduct from initial stock immediately
        if ($request->is_langsung_pakai == '1') {
            $stockData['jumlah'] -= $request->jumlah_pakai;
        }

        $item->update($stockData);

        // Record usage if applicable
        if ($request->is_langsung_pakai == '1') {
            StockAmprahanUsage::create([
                'stock_amprahan_id' => $item->id,
                'penerima_id' => $request->penerima_id,
                'kendaraan_id' => $request->kendaraan_id,
                'truck_id' => $request->truck_id,
                'buntut_id' => $request->buntut_id,
                'kapal_id' => $request->kapal_id,
                'alat_berat_id' => $request->alat_berat_id,
                'lain_lain' => $request->lain_lain,
                'jumlah' => $request->jumlah_pakai,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => $request->keterangan_pakai,
                'kilometer' => $request->kilometer,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil diperbarui' . ($request->is_langsung_pakai == '1' ? ' dan langsung diproses pemakaiannya.' : '.'));
    }

    public function destroy($id)
    {
        $item = StockAmprahan::findOrFail($id);
        $item->delete();
        
        return redirect()->route('stock-amprahan.index')->with('success', 'Data stock amprahan berhasil dihapus');
    }
    public function storeUsage(Request $request, $id)
    {
        $item = StockAmprahan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:0.01|max:' . $item->jumlah,
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'penerima_id' => 'required|exists:karyawans,id',
            'kendaraan_id' => 'nullable|exists:mobils,id',
            'truck_id' => 'nullable|exists:mobils,id',
            'buntut_id' => 'nullable|exists:mobils,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
            'lain_lain' => 'nullable|string|max:255',
            'kilometer' => 'nullable|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            $kendaraanId = $request->kendaraan_id;
            $alatBeratId = $request->alat_berat_id;

            if (!empty($kendaraanId) && !empty($alatBeratId)) {
                $validator->errors()->add('kendaraan_id', 'Pilih salah satu: kendaraan/truck atau alat berat.');
                $validator->errors()->add('alat_berat_id', 'Pilih salah satu: kendaraan/truck atau alat berat.');
            }
            
            if (!empty($request->truck_id) && !empty($request->alat_berat_id)) {
                if (!$validator->errors()->has('alat_berat_id')) {
                    $validator->errors()->add('truck_id', 'Pilih salah satu: truck atau alat berat.');
                    $validator->errors()->add('alat_berat_id', 'Pilih salah satu: truck atau alat berat.');
                }
            }
        });

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $penerima = Karyawan::findOrFail($request->penerima_id);

        // Decrement stock
        $item->jumlah -= $request->jumlah;
        $item->updated_by = Auth::id();
        $item->save();

        // Create usage record
        StockAmprahanUsage::create([
            'stock_amprahan_id' => $item->id,
            'penerima_id' => $request->penerima_id,
            'kendaraan_id' => $request->kendaraan_id,
            'truck_id' => $request->truck_id,
            'buntut_id' => $request->buntut_id,
            'kapal_id' => $request->kapal_id,
            'alat_berat_id' => $request->alat_berat_id,
            'lain_lain' => $request->lain_lain,
            'jumlah' => $request->jumlah,
            'tanggal_pengambilan' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'kilometer' => $request->kilometer,
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return redirect()->route('stock-amprahan.index')
                ->with('success', 'Pengambilan barang berhasil dicatat. Sisa stock: ' . $item->jumlah . ' ' . $item->satuan);
        }

        return redirect()->route('stock-amprahan.index')
            ->with('success', 'Pengambilan barang berhasil dicatat. Sisa stock: ' . $item->jumlah . ' ' . $item->satuan);
    }
    public function history(Request $request, $id)
    {
        $item = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages'])->findOrFail($id);
        
        // Calculate initial stock (current amount + all usages)
        $totalUsage = $item->usages->sum('jumlah');
        $initialStock = $item->jumlah + $totalUsage;

        // Addition record (initial purchase)
        $addition = (object)[
            'type' => 'Masuk',
            'is_addition' => true,
            'id' => $item->id,
            'tanggal' => $item->tanggal_beli ? $item->tanggal_beli->format('Y-m-d') : $item->created_at->format('Y-m-d'),
            'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
            'jumlah' => $initialStock,
            'keterangan' => 'Stock Masuk: ' . ($item->nomor_bukti ? 'Bukti #' . $item->nomor_bukti : 'Awal'),
            'penerima' => (object)['nama_lengkap' => '-'],
            'kendaraan' => null,
            'truck' => null,
            'buntut' => null,
            'kapal' => null,
            'alatBerat' => null,
            'kilometer' => '-',
            'createdBy' => $item->createdBy,
            'stockAmprahan' => $item
        ];

        // Usage records query
        $usagesQuery = $item->usages()->with(['penerima', 'kendaraan', 'truck', 'buntut', 'kapal', 'alatBerat', 'createdBy']);
        
        // Filter by date if provided
        if ($request->filled('from_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '<=', $request->to_date);
        }

        $usages = $usagesQuery->get()->map(function($usage) {
            $usage->type = 'Keluar';
            $usage->is_addition = false;
            $usage->tanggal = $usage->tanggal_pengambilan;
            $usage->tanggal_raw = $usage->tanggal_pengambilan;
            return $usage;
        });

        // Filter addition by date if provided
        $showAddition = true;
        if ($request->filled('from_date')) {
            $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
            if ($addition->tanggal_raw < $fromDate) $showAddition = false;
        }
        if ($request->filled('to_date')) {
            $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
            if ($addition->tanggal_raw > $toDate) $showAddition = false;
        }

        // Combine and sort
        $combined = collect();
        if ($showAddition) $combined->push($addition);
        $combined = $combined->concat($usages)->sortByDesc(function($item) {
            return $item->tanggal_raw;
        })->values();

        if ($request->ajax()) {
            $formatted = $combined->map(function ($entry) {
                $kendaraanInfo = $entry->kendaraan ? ($entry->kendaraan->nomor_polisi . ' - ' . $entry->kendaraan->merek) : '-';
                $truckInfo = $entry->truck ? ($entry->truck->nomor_polisi . ' - ' . $entry->truck->merek) : '-';
                $buntutInfo = $entry->buntut ? ($entry->buntut->nomor_polisi . ' - ' . $entry->buntut->merek) : '-';
                $kapalInfo = $entry->kapal ? $entry->kapal->nama_kapal : '-';
                $alatBeratInfo = $entry->alatBerat ? ($entry->alatBerat->kode_alat . ' - ' . $entry->alatBerat->nama . ($entry->alatBerat->merk ? ' - ' . $entry->alatBerat->merk : '')) : '-';
                $lainLainInfo = $entry->lain_lain ?? '-';
                
                return [
                    'type' => $entry->type,
                    'is_addition' => $entry->is_addition,
                    'tanggal' => date('d-m-Y', strtotime($entry->tanggal_raw)),
                    'jumlah' => $entry->jumlah,
                    'penerima' => $entry->penerima->nama_lengkap ?? '-',
                    'kendaraan' => $kendaraanInfo,
                    'truck' => $truckInfo,
                    'buntut' => $buntutInfo,
                    'kapal' => $kapalInfo,
                    'alat_berat' => $alatBeratInfo,
                    'lain_lain' => $lainLainInfo,
                    'kilometer' => $entry->kilometer ?? '-',
                    'keterangan' => $entry->keterangan,
                    'created_by' => $entry->createdBy->name ?? '-',
                ];
            });
            return response()->json($formatted);
        }

        return view('stock-amprahan.history', [
            'item' => $item,
            'history' => $combined,
            'usages' => $combined // Still pass as 'usages' for minimal view changes if needed, but renamed to 'history' is better
        ]);
    }

    public function allHistory(Request $request)
    {
        // Additions query
        $additionsQuery = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages']);
        
        if ($request->filled('from_date')) {
            $additionsQuery->where(function($q) use ($request) {
                $q->whereDate('tanggal_beli', '>=', $request->from_date)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereNull('tanggal_beli')->whereDate('created_at', '>=', $request->from_date);
                  });
            });
        }
        if ($request->filled('to_date')) {
            $additionsQuery->where(function($q) use ($request) {
                $q->whereDate('tanggal_beli', '<=', $request->to_date)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereNull('tanggal_beli')->whereDate('created_at', '<=', $request->to_date);
                  });
            });
        }
        if ($request->filled('lokasi')) {
            $additionsQuery->where('lokasi', $request->lokasi);
        }

        $additions = $additionsQuery->get()->map(function($item) {
            $totalUsage = $item->usages->sum('jumlah');
            $initialStock = $item->jumlah + $totalUsage;
            
            return (object)[
                'type' => 'Masuk',
                'is_addition' => true,
                'id' => $item->id,
                'tanggal' => $item->tanggal_beli ? $item->tanggal_beli->format('Y-m-d') : $item->created_at->format('Y-m-d'),
                'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                'jumlah' => $initialStock,
                'keterangan' => 'Stock Masuk: ' . ($item->nomor_bukti ? 'Bukti #' . $item->nomor_bukti : 'Awal'),
                'penerima' => (object)['nama_lengkap' => '-'],
                'kendaraan' => null,
                'truck' => null,
                'buntut' => null,
                'kapal' => null,
                'alatBerat' => null,
                'kilometer' => '-',
                'createdBy' => $item->createdBy,
                'stockAmprahan' => $item
            ];
        });

        // Usages query
        $usagesQuery = StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'kapal', 'alatBerat', 'createdBy']);
        
        if ($request->filled('from_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '<=', $request->to_date);
        }
        if ($request->filled('lokasi')) {
            $usagesQuery->whereHas('stockAmprahan', function($q) use ($request) {
                $q->where('lokasi', $request->lokasi);
            });
        }

        $usages = $usagesQuery->get()->map(function($usage) {
            $usage->type = 'Keluar';
            $usage->is_addition = false;
            $usage->tanggal_raw = $usage->tanggal_pengambilan;
            return $usage;
        });

        // Combine and sort
        $combined = $additions->concat($usages)->sortByDesc(function($item) {
            return $item->tanggal_raw;
        })->values();

        // Manual Pagination
        $perPage = 20;
        $page = $request->input('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage),
            $combined->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('stock-amprahan.history', [
            'history' => $paginated,
            'usages' => $paginated
        ]);
    }

    public function historyPrint(Request $request)
    {
        $id = $request->id;
        $item = null;
        
        if ($id) {
            $item = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages'])->findOrFail($id);
            
            // Calculate initial stock
            $totalUsage = $item->usages->sum('jumlah');
            $initialStock = $item->jumlah + $totalUsage;

            $addition = (object)[
                'type' => 'Masuk',
                'is_addition' => true,
                'id' => $item->id,
                'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                'jumlah' => $initialStock,
                'keterangan' => 'Stock Masuk: ' . ($item->nomor_bukti ? 'Bukti #' . $item->nomor_bukti : 'Awal'),
                'penerima' => (object)['nama_lengkap' => '-'],
                'kendaraan' => null,
                'truck' => null,
                'buntut' => null,
                'kapal' => null,
                'alatBerat' => null,
                'kilometer' => '-',
                'createdBy' => $item->createdBy,
                'stockAmprahan' => $item
            ];

            $usagesQuery = $item->usages()->with(['penerima', 'kendaraan', 'truck', 'buntut', 'kapal', 'alatBerat', 'createdBy']);
        } else {
            // All History Logic
            $additionsQuery = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages']);
            if ($request->filled('from_date')) {
                $additionsQuery->where(function($q) use ($request) {
                    $q->whereDate('tanggal_beli', '>=', $request->from_date)
                      ->orWhere(function($sq) use ($request) {
                          $sq->whereNull('tanggal_beli')->whereDate('created_at', '>=', $request->from_date);
                      });
                });
            }
            if ($request->filled('to_date')) {
                $additionsQuery->where(function($q) use ($request) {
                    $q->whereDate('tanggal_beli', '<=', $request->to_date)
                      ->orWhere(function($sq) use ($request) {
                          $sq->whereNull('tanggal_beli')->whereDate('created_at', '<=', $request->to_date);
                      });
                });
            }
            if ($request->filled('lokasi')) {
                $additionsQuery->where('lokasi', $request->lokasi);
            }
            $additions = $additionsQuery->get()->map(function($item) {
                $totalUsage = $item->usages->sum('jumlah');
                $initialStock = $item->jumlah + $totalUsage;
                return (object)[
                    'type' => 'Masuk',
                    'is_addition' => true,
                    'id' => $item->id,
                    'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                    'jumlah' => $initialStock,
                    'keterangan' => 'Stock Masuk: ' . ($item->nomor_bukti ? 'Bukti #' . $item->nomor_bukti : 'Awal'),
                    'penerima' => (object)['nama_lengkap' => '-'],
                    'kendaraan' => null,
                    'truck' => null,
                    'buntut' => null,
                    'kapal' => null,
                    'alatBerat' => null,
                    'kilometer' => '-',
                    'createdBy' => $item->createdBy,
                    'stockAmprahan' => $item
                ];
            });

            $usagesQuery = StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'kapal', 'alatBerat', 'createdBy']);
        }

        if ($request->filled('from_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '<=', $request->to_date);
        }
        if ($request->filled('lokasi')) {
            $usagesQuery->whereHas('stockAmprahan', function($q) use ($request) {
                $q->where('lokasi', $request->lokasi);
            });
        }

        $usages = $usagesQuery->get()->map(function($usage) {
            $usage->type = 'Keluar';
            $usage->is_addition = false;
            $usage->tanggal_raw = $usage->tanggal_pengambilan;
            return $usage;
        });

        if ($id) {
            // Filter addition for single item
            $showAddition = true;
            if ($request->filled('from_date')) {
                $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                if ($addition->tanggal_raw < $fromDate) $showAddition = false;
            }
            if ($request->filled('to_date')) {
                $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                if ($addition->tanggal_raw > $toDate) $showAddition = false;
            }
            $combined = collect();
            if ($showAddition) $combined->push($addition);
            $combined = $combined->concat($usages);
        } else {
            $combined = $additions->concat($usages);
        }

        $history = $combined->sortByDesc('tanggal_raw')->values();

        return view('stock-amprahan.history-print', [
            'item' => $item,
            'history' => $history
        ]);
    }

    public function generateNomorPranota()
    {
        try {
            $kode = 'PTP'; // Pranota Transfer Pelabuhan/Pergudangan/Perlengkapan? (User requested PTP)
            $bulan = now()->format('m');
            $tahun = now()->format('y');
            
            $prefix = "{$kode}-{$bulan}-{$tahun}-";
            $lastPranota = \App\Models\PranotaStock::where('nomor_pranota', 'like', $prefix . '%')
                ->orderBy('nomor_pranota', 'desc')
                ->first();
            
            if ($lastPranota) {
                $lastNumber = (int) substr($lastPranota->nomor_pranota, -6);
                $runningNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $runningNumber = '000001';
            }
            
            $nomorPranota = "{$prefix}{$runningNumber}";
            
            return response()->json([
                'success' => true,
                'nomor_pranota' => $nomorPranota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor pranota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function masukPranota(Request $request)
    {
        try {
            $data = $request->validate([
                'nomor_pranota' => 'required|string|unique:pranota_stocks,nomor_pranota',
                'tanggal_pranota' => 'required|date',
                'nomor_accurate' => 'nullable|string',
                'vendor' => 'nullable|string',
                'rekening' => 'nullable|string',
                'penerima' => 'nullable|string',
                'adjustment' => 'nullable|numeric',
                'keterangan' => 'nullable|string',
                'items' => 'required|array',
            ]);

            $pranota = \App\Models\PranotaStock::create([
                'nomor_pranota' => $data['nomor_pranota'],
                'tanggal_pranota' => $data['tanggal_pranota'],
                'nomor_accurate' => $data['nomor_accurate'],
                'vendor' => $data['vendor'],
                'rekening' => $data['rekening'],
                'penerima' => $data['penerima'],
                'adjustment' => $data['adjustment'] ?? 0,
                'keterangan' => $data['keterangan'],
                'items' => $data['items'],
                'status' => 'approved', // Langsung approved atau draft? User di pranota lain biasanya approved
                'created_by' => Auth::id(),
            ]);

            // Update status_pranota pada item yang bersangkutan
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (isset($item['id'])) {
                        \App\Models\StockAmprahan::where('id', $item['id'])->update(['status_pranota' => 'Sudah']);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memasukkan ke pranota',
                'redirect' => route('pranota-stock.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memasukkan ke pranota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pranotaIndex(Request $request)
    {
        $query = \App\Models\PranotaStock::with('creator')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhere('nomor_accurate', 'like', "%{$search}%");
        }

        $items = $query->paginate(20);
        return view('pranota-stock.index', compact('items'));
    }

    public function pranotaPrint($id)
    {
        $pranota = \App\Models\PranotaStock::with('creator')->findOrFail($id);

        // Hydrate items with fresh data from DB to ensure no empty columns
        if (is_array($pranota->items)) {
            $itemIds = collect($pranota->items)->pluck('id')->filter()->toArray();
            $stockItems = \App\Models\StockAmprahan::with(['usages.kendaraan', 'usages.truck', 'usages.buntut', 'usages.kapal', 'usages.alatBerat', 'masterNamaBarangAmprahan'])
                ->whereIn('id', $itemIds)
                ->get()
                ->keyBy('id');
                
            $hydratedItems = array_map(function($it) use ($stockItems) {
                $id = $it['id'] ?? null;
                if ($id && isset($stockItems[$id])) {
                    $item = $stockItems[$id];
                    
                    // Compute Reference from usages
                    $refItems = [];
                    $refType = null;
                    $firstUsage = $item->usages->first();
                    if ($firstUsage) {
                        if ($firstUsage->kapal) {
                            $refItems[] = $firstUsage->kapal->nama_kapal;
                            $refType = 'Kapal';
                        }
                        if ($firstUsage->alatBerat) {
                            $refItems[] = $firstUsage->alatBerat->nama;
                            if (!$refType) $refType = 'Alat Berat';
                        }
                        if ($firstUsage->kendaraan) {
                            $refItems[] = $firstUsage->kendaraan->nomor_polisi;
                            if (!$refType) $refType = 'Kendaraan';
                        }
                        if ($firstUsage->truck) {
                            $refItems[] = 'Truck: ' . $firstUsage->truck->nomor_polisi;
                            if (!$refType) $refType = 'Truck';
                        }
                        if ($firstUsage->buntut) {
                            $refItems[] = 'Buntut: ' . ($firstUsage->buntut->no_kir ?: $firstUsage->buntut->nomor_polisi);
                            if (!$refType) $refType = 'Buntut';
                        }
                        if ($firstUsage->lain_lain) {
                            $refItems[] = $firstUsage->lain_lain;
                            if (!$refType) $refType = 'Lain-lain';
                        }
                    }
                    $reference = count($refItems) > 0 ? implode(' / ', $refItems) : '-';
                    
                    return array_merge($it, [
                        'tanggal' => $item->tanggal_beli ? $item->tanggal_beli->format('Y-m-d') : ($item->created_at ? $item->created_at->format('Y-m-d') : '-'),
                        'type' => $item->type_amprahan ?? '-',
                        'reference' => $reference,
                        'reference_type' => $refType,
                        'keterangan' => $item->keterangan ?? '-',
                        'nama_barang' => $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? ($it['nama_barang'] ?? '-')),
                        'harga' => $item->harga_satuan ?? ($it['harga'] ?? 0),
                        'adjustment' => $item->adjustment ?? ($it['adjustment'] ?? 0),
                        'satuan' => $item->satuan ?? ($it['satuan'] ?? '-'),
                    ]);
                }
                return $it;
            }, $pranota->items);
            
            $pranota->items = $hydratedItems;
        }

        return view('pranota-stock.print', compact('pranota'));
    }

    public function pranotaDestroy($id)
    {
        try {
            $pranota = \App\Models\PranotaStock::findOrFail($id);
            
            // Revert status_pranota pada item
            if (is_array($pranota->items)) {
                foreach ($pranota->items as $item) {
                    if (isset($item['id'])) {
                        \App\Models\StockAmprahan::where('id', $item['id'])->update(['status_pranota' => 'Belum']);
                    }
                }
            }

            $pranota->delete();
            return redirect()->route('pranota-stock.index')->with('success', 'Pranota berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus pranota: ' . $e->getMessage());
        }
    }
    public function togglePranotaStatus($id)
    {
        $item = StockAmprahan::findOrFail($id);
        $item->status_pranota = ($item->status_pranota == 'Sudah') ? 'Belum' : 'Sudah';
        $item->save();
        
        return back()->with('success', 'Status Pranota berhasil diperbarui.');
    }
}
