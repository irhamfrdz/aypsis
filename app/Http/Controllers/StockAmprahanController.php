<?php

namespace App\Http\Controllers;

use App\Exports\StockAmprahanExport;
use App\Exports\StockAmprahanHistoryExport;
use App\Models\AlatBerat;
use App\Models\Bank;
use App\Models\Karyawan;
use App\Models\MasterChasisBatam;
use App\Models\MasterGudangAmprahan;
use App\Models\MasterKapal;
use App\Models\MasterNamaBarangAmprahan;
use App\Models\Mobil;
use App\Models\StockAmprahan;
use App\Models\StockAmprahanUsage;
use App\Models\VendorAmprahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StockAmprahanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = StockAmprahan::with(['masterNamaBarangAmprahan', 'vendorAmprahan', 'createdBy', 'updatedBy', 'usages.kendaraan', 'usages.truck', 'usages.buntut', 'usages.chasisBatam', 'usages.kapal', 'usages.alatBerat'])
            ->withSum('usages', 'jumlah')
            ->latest();

        // Filter based on Karyawan Cabang (Branch)
        $user = Auth::user();
        $isBatamUser = false;
        $isJakartaUser = false;

        // Admin or users with specific roles might see everything
        $isRestricted = $user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin');

        if ($isRestricted) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $isBatamUser = true;
                $query->where('lokasi', 'like', '%BATAM%');
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%'.$search.'%')
                    ->orWhere('nomor_bukti', 'like', '%'.$search.'%')
                    ->orWhereHas('masterNamaBarangAmprahan', function ($q) use ($search) {
                        $q->where('nama_barang', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('lokasi')) {
            $lokasi = $request->lokasi;
            if ($lokasi === 'LAINNYA') {
                $query->where(function ($q) {
                    $q->whereNotIn('lokasi', ['KANTOR AYP JAKARTA', 'KANTOR AYP BATAM'])
                        ->orWhereNull('lokasi');
                });
            } else {
                $query->where('lokasi', $lokasi);
            }
        }

        if ($request->filled('type_amprahan')) {
            $query->where('type_amprahan', $request->type_amprahan);
        }

        if ($request->filled('from_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_beli', '>=', $request->from_date)
                    ->orWhere(function ($sq) use ($request) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '>=', $request->from_date);
                    });
            });
        }

        if ($request->filled('to_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('tanggal_beli', '<=', $request->to_date)
                    ->orWhere(function ($sq) use ($request) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '<=', $request->to_date);
                    });
            });
        }

        $selectedMobil = null;
        if ($request->filled('mobil_id')) {
            $mobilId = $request->mobil_id;
            $selectedMobil = \App\Models\Mobil::find($mobilId);

            $query->whereHas('usages', function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            });

            // Replace withSum to only count usage for this specific plate
            $query->withSum(['usages as usages_sum_jumlah' => function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            }], 'jumlah');
        }

        $items = $query->paginate(20)->withQueryString();

        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $kendaraans = Mobil::orderBy('nomor_polisi')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();

        // Stats for Cards (Respect branch filtering)
        $statsQuery = StockAmprahan::query();
        if ($isBatamUser) {
            $statsQuery->where('lokasi', 'like', '%BATAM%');
        }

        $stats = [
            'total_qty' => (clone $statsQuery)->sum('jumlah'),
            'total_jenis' => (clone $statsQuery)->count(),
            'jakarta' => (clone $statsQuery)->where('lokasi', 'KANTOR AYP JAKARTA')->sum('jumlah'),
            'batam' => (clone $statsQuery)->where('lokasi', 'KANTOR AYP BATAM')->sum('jumlah'),
            'lainnya' => (clone $statsQuery)->where(function ($q) {
                $q->whereNotIn('lokasi', ['KANTOR AYP JAKARTA', 'KANTOR AYP BATAM'])
                    ->orWhereNull('lokasi');
            })->sum('jumlah'),
            'usage_by_plate' => 0,
        ];

        if ($request->filled('mobil_id')) {
            $mobilId = $request->mobil_id;
            $stats['usage_by_plate'] = \App\Models\StockAmprahanUsage::where(function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            })->sum('jumlah');
        }

        $masterItems = \App\Models\MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $uniqueNamaBarang = \App\Models\StockAmprahan::select('nama_barang')
            ->whereNotNull('nama_barang')
            ->where('nama_barang', '!=', '')
            ->distinct()
            ->orderBy('nama_barang')
            ->pluck('nama_barang');
        $vendors = \App\Models\VendorAmprahan::orderBy('nama_toko')->get();
        $chasis = MasterChasisBatam::orderBy('kode')->get();

        $banks = Bank::orderBy('name')->pluck('name')->toArray();

        return view('stock-amprahan.index', compact('items', 'karyawans', 'kendaraans', 'alatBerats', 'kapals', 'search', 'stats', 'masterItems', 'uniqueNamaBarang', 'selectedMobil', 'banks', 'vendors', 'chasis'));
    }

    public function exportExcel(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'lokasi' => $request->get('lokasi'),
            'type_amprahan' => $request->get('type_amprahan'),
            'mobil_id' => $request->get('mobil_id'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        $fileName = 'Stock_Amprahan_'.date('Ymd_His').'.xlsx';
        if ($request->filled('mobil_id')) {
            $mobil = Mobil::find($request->mobil_id);
            if ($mobil) {
                $fileName = 'Stock_Amprahan_'.str_replace(' ', '_', $mobil->nomor_polisi).'_'.date('Ymd_His').'.xlsx';
            }
        }

        return Excel::download(new StockAmprahanExport($filters), $fileName);
    }

    public function create()
    {
        $masterItems = MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $gudangItems = MasterGudangAmprahan::where('status', 'active')->orderBy('nama_gudang')->get();

        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $kendaraans = Mobil::orderBy('nomor_polisi')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();
        $vendorAmprahans = VendorAmprahan::orderBy('nama_toko')->get();
        $chasis = MasterChasisBatam::orderBy('kode')->get();

        $mobils = $kendaraans;

        return view('stock-amprahan.create', compact('masterItems', 'gudangItems', 'karyawans', 'kendaraans', 'mobils', 'kapals', 'alatBerats', 'vendorAmprahans', 'chasis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_bukti' => 'nullable|string|max:255',
            'tanggal_beli' => 'nullable|date',
            'type_amprahan' => 'required|in:Pemakaian,Perbaikan,Perlengkapan,Peralatan,Transportasi,Inventory',
            'nama_barang' => 'required|string|max:255',
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'harga_satuan' => 'nullable|numeric|min:0',
            'adjustment' => 'nullable|numeric',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'vendor_amprahan_id' => 'required|exists:vendor_amprahans,id',

            // Langsung Pakai Fields
            'is_langsung_pakai' => 'nullable',
            'penerima_id' => 'nullable|required_if:is_langsung_pakai,1|exists:karyawans,id',
            'kendaraan_id' => 'nullable|exists:mobils,id',
            'truck_id' => 'nullable|exists:mobils,id',
            'buntut_id' => 'nullable|exists:master_chasis_batams,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
            'kantor' => 'nullable|string|max:255',
            'tanggal_pengambilan' => 'nullable|required_if:is_langsung_pakai,1|date',
            'jumlah_pakai' => 'nullable|required_if:is_langsung_pakai,1|numeric|min:0',
            'keterangan_pakai' => 'nullable|required_if:is_langsung_pakai,1|string',
            'kilometer' => 'nullable|numeric|min:0',
            'odometer' => 'nullable|integer|min:0',
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
            'vendor_amprahan_id' => $data['vendor_amprahan_id'],
            'created_by' => $data['created_by'],
        ];

        // If langsung pakai, deduct from initial stock immediately
        if ($request->is_langsung_pakai == '1') {
            $stockData['jumlah'] -= $request->jumlah_pakai;
        }

        $stock = StockAmprahan::create($stockData);

        // Record usage if applicable
        if ($request->is_langsung_pakai == '1') {
            $buntutIdInput = $request->buntut_id;
            $buntutId = null;
            $chasisBatamId = null;

            if ($buntutIdInput) {
                if (str_starts_with($buntutIdInput, 'mobil_')) {
                    $buntutId = str_replace('mobil_', '', $buntutIdInput);
                } elseif (str_starts_with($buntutIdInput, 'chasis_')) {
                    $chasisBatamId = str_replace('chasis_', '', $buntutIdInput);
                }
            }

            StockAmprahanUsage::create([
                'stock_amprahan_id' => $stock->id,
                'penerima_id' => $request->penerima_id,
                'kendaraan_id' => $request->kendaraan_id,
                'truck_id' => $request->truck_id,
                'buntut_id' => $buntutId,
                'chasis_batam_id' => $chasisBatamId,
                'kapal_id' => $request->kapal_id,
                'alat_berat_id' => $request->alat_berat_id,
                'kantor' => $request->kantor,
                'jumlah' => $request->jumlah_pakai,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => $request->keterangan_pakai,
                'kilometer' => $request->kilometer,
                'odometer' => $request->odometer,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil ditambahkan'.($request->is_langsung_pakai == '1' ? ' dan langsung diproses pemakaiannya.' : '.'));
    }

    public function show($id)
    {
        $item = StockAmprahan::with(['masterNamaBarangAmprahan', 'vendorAmprahan', 'createdBy', 'updatedBy'])->findOrFail($id);

        return view('stock-amprahan.show', compact('item'));
    }

    public function edit($id)
    {
        $item = StockAmprahan::with(['usages' => fn ($q) => $q->orderBy('id', 'asc')])->findOrFail($id);

        // Let's identify the 'Direct Usage' (usually the oldest one created)
        $directUsage = $item->usages->first();

        // If there's a usage, the 'jumlah' in the form should show the Total quantity (Stock + that Usage)
        // because the update logic will subtract it again.
        if ($directUsage) {
            $item->jumlah += $directUsage->jumlah;
        }

        $masterItems = MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $gudangItems = MasterGudangAmprahan::where('status', 'active')->orderBy('nama_gudang')->get();
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $kendaraans = Mobil::orderBy('nomor_polisi')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();
        $vendorAmprahans = VendorAmprahan::orderBy('nama_toko')->get();
        $chasis = MasterChasisBatam::orderBy('kode')->get();

        return view('stock-amprahan.edit', compact('item', 'directUsage', 'masterItems', 'gudangItems', 'karyawans', 'kendaraans', 'kapals', 'alatBerats', 'vendorAmprahans', 'chasis'));
    }

    public function update(Request $request, $id)
    {
        $item = StockAmprahan::with('usages')->findOrFail($id);

        $data = $request->validate([
            'nomor_bukti' => 'nullable|string|max:255',
            'tanggal_beli' => 'nullable|date',
            'type_amprahan' => 'required|in:Pemakaian,Perbaikan,Perlengkapan,Peralatan,Transportasi,Inventory',
            'nama_barang' => 'required|string|max:255',
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'harga_satuan' => 'nullable|numeric|min:0',
            'adjustment' => 'nullable|numeric',
            'jumlah' => 'required|numeric|min:0', // This is the TOTAL quantity in the form
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'vendor_amprahan_id' => 'required|exists:vendor_amprahans,id',
            'is_langsung_pakai' => 'nullable',
            'penerima_id' => 'nullable|required_if:is_langsung_pakai,1|exists:karyawans,id',
            'kendaraan_id' => 'nullable|exists:mobils,id',
            'truck_id' => 'nullable|exists:mobils,id',
            'buntut_id' => 'nullable|exists:master_chasis_batams,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
            'kantor' => 'nullable|string|max:255',
            'tanggal_pengambilan' => 'nullable|required_if:is_langsung_pakai,1|date',
            'jumlah_pakai' => 'nullable|required_if:is_langsung_pakai,1|numeric|min:0',
            'keterangan_pakai' => 'nullable|required_if:is_langsung_pakai,1|string',
            'kilometer' => 'nullable|numeric|min:0',
            'odometer' => 'nullable|integer|min:0',
        ]);

        $data['updated_by'] = Auth::id();

        // Manual validation for jumlah_pakai against the entered TOTAL jumlah
        if ($request->is_langsung_pakai == '1') {
            if ($request->jumlah_pakai > $data['jumlah']) {
                return redirect()->back()->withErrors(['jumlah_pakai' => 'Jumlah pakai tidak boleh lebih besar dari jumlah stock.'])->withInput();
            }
        }

        // Identify existing direct usage (oldest)
        $existingDirectUsage = $item->usages()->orderBy('id', 'asc')->first();

        // Handle Stock Update
        $finalJumlah = $data['jumlah'];
        if ($request->is_langsung_pakai == '1') {
            $finalJumlah -= $request->jumlah_pakai;
        }

        $stockData = [
            'nomor_bukti' => $data['nomor_bukti'],
            'tanggal_beli' => $data['tanggal_beli'],
            'type_amprahan' => $data['type_amprahan'],
            'nama_barang' => $data['nama_barang'],
            'master_nama_barang_amprahan_id' => $data['master_nama_barang_amprahan_id'],
            'harga_satuan' => $data['harga_satuan'],
            'adjustment' => $data['adjustment'] ?? 0,
            'jumlah' => $finalJumlah,
            'satuan' => $data['satuan'],
            'lokasi' => $data['lokasi'],
            'keterangan' => $data['keterangan'],
            'vendor_amprahan_id' => $data['vendor_amprahan_id'],
            'updated_by' => $data['updated_by'],
        ];

        $item->update($stockData);

        // Handle Usage Record
        if ($request->is_langsung_pakai == '1') {
            $buntutIdInput = $request->buntut_id;
            $buntutId = null;
            $chasisBatamId = null;

            if ($buntutIdInput) {
                if (str_starts_with($buntutIdInput, 'mobil_')) {
                    $buntutId = str_replace('mobil_', '', $buntutIdInput);
                } elseif (str_starts_with($buntutIdInput, 'chasis_')) {
                    $chasisBatamId = str_replace('chasis_', '', $buntutIdInput);
                }
            }

            $usageData = [
                'penerima_id' => $request->penerima_id,
                'kendaraan_id' => $request->kendaraan_id,
                'truck_id' => $request->truck_id,
                'buntut_id' => $buntutId,
                'chasis_batam_id' => $chasisBatamId,
                'kapal_id' => $request->kapal_id,
                'alat_berat_id' => $request->alat_berat_id,
                'kantor' => $request->kantor,
                'jumlah' => $request->jumlah_pakai,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => $request->keterangan_pakai,
                'kilometer' => $request->kilometer,
                'odometer' => $request->odometer,
            ];

            if ($existingDirectUsage) {
                $existingDirectUsage->update($usageData);
            } else {
                $usageData['stock_amprahan_id'] = $item->id;
                $usageData['created_by'] = Auth::id();
                StockAmprahanUsage::create($usageData);
            }
        } else {
            // If unchecked, delete the direct usage if it exists
            if ($existingDirectUsage) {
                $existingDirectUsage->delete();
            }
        }

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil diperbarui.');
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
            'jumlah' => 'required|numeric|min:0.01|max:'.$item->jumlah,
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'penerima_id' => 'required|exists:karyawans,id',
            'kendaraan_id' => 'nullable|exists:mobils,id',
            'truck_id' => 'nullable|exists:mobils,id',
            'buntut_id' => 'nullable|string|max:50',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
            'kantor' => 'nullable|string|max:255',
            'kilometer' => 'nullable|numeric|min:0',
            'odometer' => 'nullable|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            $kendaraanId = $request->kendaraan_id;
            $alatBeratId = $request->alat_berat_id;

            if (! empty($kendaraanId) && ! empty($alatBeratId)) {
                $validator->errors()->add('kendaraan_id', 'Pilih salah satu: kendaraan/truck atau alat berat.');
                $validator->errors()->add('alat_berat_id', 'Pilih salah satu: kendaraan/truck atau alat berat.');
            }

            if (! empty($request->truck_id) && ! empty($request->alat_berat_id)) {
                if (! $validator->errors()->has('alat_berat_id')) {
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

        $buntutIdInput = $request->buntut_id;
        $buntutId = null;
        $chasisBatamId = null;

        if ($buntutIdInput) {
            if (str_starts_with($buntutIdInput, 'mobil_')) {
                $buntutId = str_replace('mobil_', '', $buntutIdInput);
            } elseif (str_starts_with($buntutIdInput, 'chasis_')) {
                $chasisBatamId = str_replace('chasis_', '', $buntutIdInput);
            }
        }

        // Create usage record
        StockAmprahanUsage::create([
            'stock_amprahan_id' => $item->id,
            'penerima_id' => $request->penerima_id,
            'kendaraan_id' => $request->kendaraan_id,
            'truck_id' => $request->truck_id,
            'buntut_id' => $buntutId,
            'chasis_batam_id' => $chasisBatamId,
            'kapal_id' => $request->kapal_id,
            'alat_berat_id' => $request->alat_berat_id,
            'kantor' => $request->kantor,
            'jumlah' => $request->jumlah,
            'tanggal_pengambilan' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'kilometer' => $request->kilometer,
            'odometer' => $request->odometer,
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return redirect()->route('stock-amprahan.index')
                ->with('success', 'Pengambilan barang berhasil dicatat. Sisa stock: '.$item->jumlah.' '.$item->satuan);
        }

        return redirect()->route('stock-amprahan.index')
            ->with('success', 'Pengambilan barang berhasil dicatat. Sisa stock: '.$item->jumlah.' '.$item->satuan);
    }

    public function destroyUsage($id)
    {
        $usage = StockAmprahanUsage::findOrFail($id);

        // Check if user is restricted to a branch (Batam) and trying to delete usage of non-Batam stock
        $user = Auth::user();
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM' && strpos(strtoupper($usage->stockAmprahan->lokasi ?? ''), 'BATAM') === false) {
                abort(403, 'Unauthorized access to this branch data.');
            }
        }

        DB::transaction(function () use ($usage) {
            // Restore quantity to parent stock
            $stock = $usage->stockAmprahan;
            if ($stock) {
                $stock->increment('jumlah', $usage->jumlah);
            }

            $usage->delete();
        });

        return redirect()->back()->with('success', 'Catatan pemakaian berhasil dihapus dan jumlah stock dikembalikan.');
    }

    public function history(Request $request, $id)
    {
        $item = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages'])->findOrFail($id);

        // Authorization check
        $user = Auth::user();
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM' && strpos(strtoupper($item->lokasi ?? ''), 'BATAM') === false) {
                abort(403, 'Unauthorized access to this branch data.');
            }
        }

        // Calculate initial stock (current amount + all usages)
        $totalUsage = $item->usages->sum('jumlah');
        $initialStock = $item->jumlah + $totalUsage;

        // Addition record (initial purchase)
        $addition = (object) [
            'type' => 'Masuk',
            'is_addition' => true,
            'id' => $item->id,
            'tanggal' => $item->tanggal_beli ? $item->tanggal_beli->format('Y-m-d') : $item->created_at->format('Y-m-d'),
            'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
            'jumlah' => $initialStock,
            'keterangan' => 'Stock Masuk: '.($item->nomor_bukti ? 'Bukti #'.$item->nomor_bukti : 'Awal'),
            'penerima' => (object) ['nama_lengkap' => '-'],
            'kendaraan' => null,
            'truck' => null,
            'buntut' => null,
            'chasisBatam' => null,
            'kapal' => null,
            'alatBerat' => null,
            'kilometer' => '-',
            'createdBy' => $item->createdBy,
            'stockAmprahan' => $item,
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

        // Filter by Plat Mobil
        if ($request->filled('mobil_id')) {
            $mobilId = $request->mobil_id;
            $usagesQuery->where(function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            });
        }

        $usages = $usagesQuery->get()->map(function ($usage) {
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
            if ($addition->tanggal_raw < $fromDate) {
                $showAddition = false;
            }
        }
        if ($request->filled('to_date')) {
            $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
            if ($addition->tanggal_raw > $toDate) {
                $showAddition = false;
            }
        }

        if ($request->filled('mobil_id')) {
            $showAddition = false;
        }

        // Combine and sort
        $combined = collect();
        if ($showAddition) {
            $combined->push($addition);
        }
        $combined = $combined->concat($usages)->sortByDesc(function ($item) {
            return $item->tanggal_raw;
        })->values();

        if ($request->ajax()) {
            $formatted = $combined->map(function ($entry) {
                $kendaraanInfo = $entry->kendaraan ? ($entry->kendaraan->nomor_polisi.' - '.$entry->kendaraan->merek) : '-';
                $truckInfo = $entry->truck ? ($entry->truck->nomor_polisi.' - '.$entry->truck->merek) : '-';
                $buntutInfo = '-';
                if ($entry->chasisBatam) {
                    $buntutInfo = $entry->chasisBatam->kode.($entry->chasisBatam->tipe ? ' ('.$entry->chasisBatam->tipe.')' : '');
                } elseif ($entry->buntut) {
                    $buntutInfo = $entry->buntut->no_kir ?: ($entry->buntut->nomor_polisi ?: '-');
                }
                $kapalInfo = $entry->kapal ? $entry->kapal->nama_kapal : '-';
                $alatBeratInfo = $entry->alatBerat ? ($entry->alatBerat->kode_alat.' - '.$entry->alatBerat->nama.($entry->alatBerat->merk ? ' - '.$entry->alatBerat->merk : '')) : '-';
                $kantorInfo = $entry->kantor ?? '-';

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
                    'kantor' => $kantorInfo,
                    'kilometer' => $entry->kilometer ?? '-',
                    'keterangan' => $entry->keterangan,
                    'created_by' => $entry->createdBy->name ?? '-',
                ];
            });

            return response()->json($formatted);
        }

        $kendaraans = Mobil::orderBy('nomor_polisi')->get();

        return view('stock-amprahan.history', [
            'item' => $item,
            'history' => $combined,
            'usages' => $combined,
            'kendaraans' => $kendaraans,
        ]);
    }

    public function allHistory(Request $request)
    {
        // Additions query
        $additionsQuery = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages']);

        // Filter based on Karyawan Cabang (Branch)
        $user = Auth::user();
        $isRestricted = $user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin');

        if ($isRestricted) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $additionsQuery->where('lokasi', 'like', '%BATAM%');
            }
        }

        if ($request->filled('from_date')) {
            $additionsQuery->where(function ($q) use ($request) {
                $q->whereDate('tanggal_beli', '>=', $request->from_date)
                    ->orWhere(function ($sq) use ($request) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '>=', $request->from_date);
                    });
            });
        }
        if ($request->filled('to_date')) {
            $additionsQuery->where(function ($q) use ($request) {
                $q->whereDate('tanggal_beli', '<=', $request->to_date)
                    ->orWhere(function ($sq) use ($request) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '<=', $request->to_date);
                    });
            });
        }
        if ($request->filled('lokasi')) {
            $additionsQuery->where('lokasi', $request->lokasi);
        }

        $additions = collect();
        if (! $request->filled('mobil_id')) {
            $additions = $additionsQuery->get()->map(function ($item) {
                $totalUsage = $item->usages->sum('jumlah');
                $initialStock = $item->jumlah + $totalUsage;

                return (object) [
                    'type' => 'Masuk',
                    'is_addition' => true,
                    'id' => $item->id,
                    'tanggal' => $item->tanggal_beli ? $item->tanggal_beli->format('Y-m-d') : $item->created_at->format('Y-m-d'),
                    'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                    'jumlah' => $initialStock,
                    'keterangan' => 'Stock Masuk: '.($item->nomor_bukti ? 'Bukti #'.$item->nomor_bukti : 'Awal'),
                    'penerima' => (object) ['nama_lengkap' => '-'],
                    'kendaraan' => null,
                    'truck' => null,
                    'buntut' => null,
                    'chasisBatam' => null,
                    'kapal' => null,
                    'alatBerat' => null,
                    'kilometer' => '-',
                    'createdBy' => $item->createdBy,
                    'stockAmprahan' => $item,
                ];
            });
        }

        // Usages query
        $usagesQuery = StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'chasisBatam', 'kapal', 'alatBerat', 'createdBy']);

        // Filter based on Karyawan Cabang (Branch)
        if ($isRestricted) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $usagesQuery->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        if ($request->filled('from_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '<=', $request->to_date);
        }
        if ($request->filled('lokasi')) {
            $usagesQuery->whereHas('stockAmprahan', function ($q) use ($request) {
                $q->where('lokasi', $request->lokasi);
            });
        }

        // Filter by Plat Mobil
        if ($request->filled('mobil_id')) {
            $mobilId = $request->mobil_id;
            $usagesQuery->where(function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            });
        }

        $usages = $usagesQuery->get()->map(function ($usage) {
            $usage->type = 'Keluar';
            $usage->is_addition = false;
            $usage->tanggal_raw = $usage->tanggal_pengambilan;

            return $usage;
        });

        // Combine and sort
        $combined = $additions->concat($usages)->sortByDesc(function ($item) {
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

        $kendaraans = Mobil::orderBy('nomor_polisi')->get();

        return view('stock-amprahan.history', [
            'history' => $paginated,
            'usages' => $paginated,
            'kendaraans' => $kendaraans,
        ]);
    }

    public function historyPrint(Request $request)
    {
        $id = $request->id;
        $item = null;

        if ($id) {
            $item = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages'])->findOrFail($id);

            // Authorization check
            $user = Auth::user();
            if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
                $cabang = strtoupper($user->karyawan->cabang);
                if ($cabang === 'BATAM' && strpos(strtoupper($item->lokasi ?? ''), 'BATAM') === false) {
                    abort(403, 'Unauthorized access to this branch data.');
                }
            }

            // Calculate initial stock
            $totalUsage = $item->usages->sum('jumlah');
            $initialStock = $item->jumlah + $totalUsage;

            $addition = (object) [
                'type' => 'Masuk',
                'is_addition' => true,
                'id' => $item->id,
                'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                'jumlah' => $initialStock,
                'keterangan' => 'Stock Masuk: '.($item->nomor_bukti ? 'Bukti #'.$item->nomor_bukti : 'Awal'),
                'penerima' => (object) ['nama_lengkap' => '-'],
                'kendaraan' => null,
                'truck' => null,
                'buntut' => null,
                'chasisBatam' => null,
                'kapal' => null,
                'alatBerat' => null,
                'kilometer' => '-',
                'createdBy' => $item->createdBy,
                'stockAmprahan' => $item,
            ];

            $showAddition = true;
            if ($request->filled('mobil_id')) {
                $showAddition = false;
            }
            if ($request->filled('from_date') && \Carbon\Carbon::parse($request->from_date)->startOfDay() > $addition->tanggal_raw) {
                $showAddition = false;
            }
            if ($request->filled('to_date') && \Carbon\Carbon::parse($request->to_date)->endOfDay() < $addition->tanggal_raw) {
                $showAddition = false;
            }

            $usagesQuery = $item->usages()->with(['penerima', 'kendaraan', 'truck', 'buntut', 'kapal', 'alatBerat', 'createdBy']);
        } else {
            // All History Logic
            $additionsQuery = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'usages']);

            // Filter based on Karyawan Cabang (Branch)
            $user = Auth::user();
            if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
                $cabang = strtoupper($user->karyawan->cabang);
                if ($cabang === 'BATAM') {
                    $additionsQuery->where('lokasi', 'like', '%BATAM%');
                }
            }

            if ($request->filled('from_date')) {
                $additionsQuery->where(function ($q) use ($request) {
                    $q->whereDate('tanggal_beli', '>=', $request->from_date)
                        ->orWhere(function ($sq) use ($request) {
                            $sq->whereNull('tanggal_beli')->whereDate('created_at', '>=', $request->from_date);
                        });
                });
            }
            if ($request->filled('to_date')) {
                $additionsQuery->where(function ($q) use ($request) {
                    $q->whereDate('tanggal_beli', '<=', $request->to_date)
                        ->orWhere(function ($sq) use ($request) {
                            $sq->whereNull('tanggal_beli')->whereDate('created_at', '<=', $request->to_date);
                        });
                });
            }
            if ($request->filled('lokasi')) {
                $additionsQuery->where('lokasi', $request->lokasi);
            }
            $additions = collect();
            if (! $request->filled('mobil_id')) {
                $additions = $additionsQuery->get()->map(function ($item) {
                    $totalUsage = $item->usages->sum('jumlah');
                    $initialStock = $item->jumlah + $totalUsage;

                    return (object) [
                        'type' => 'Masuk',
                        'is_addition' => true,
                        'id' => $item->id,
                        'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                        'jumlah' => $initialStock,
                        'keterangan' => 'Stock Masuk: '.($item->nomor_bukti ? 'Bukti #'.$item->nomor_bukti : 'Awal'),
                        'penerima' => (object) ['nama_lengkap' => '-'],
                        'kendaraan' => null,
                        'truck' => null,
                        'buntut' => null,
                        'chasisBatam' => null,
                        'kapal' => null,
                        'alatBerat' => null,
                        'kilometer' => '-',
                        'createdBy' => $item->createdBy,
                        'stockAmprahan' => $item,
                    ];
                });
            }

            $usagesQuery = StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'chasisBatam', 'kapal', 'alatBerat', 'createdBy']);
        }

        if ($request->filled('from_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $usagesQuery->whereDate('tanggal_pengambilan', '<=', $request->to_date);
        }
        if ($request->filled('lokasi')) {
            $usagesQuery->whereHas('stockAmprahan', function ($q) use ($request) {
                $q->where('lokasi', $request->lokasi);
            });
        }

        // Filter by Plat Mobil
        if ($request->filled('mobil_id')) {
            $mobilId = $request->mobil_id;
            $usagesQuery->where(function ($q) use ($mobilId) {
                $q->where('kendaraan_id', $mobilId)
                    ->orWhere('truck_id', $mobilId)
                    ->orWhere('buntut_id', $mobilId);
            });
        }

        // Filter based on Karyawan Cabang (Branch)
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $usagesQuery->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        $usages = $usagesQuery->get()->map(function ($usage) {
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
                if ($addition->tanggal_raw < $fromDate) {
                    $showAddition = false;
                }
            }
            if ($request->filled('to_date')) {
                $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                if ($addition->tanggal_raw > $toDate) {
                    $showAddition = false;
                }
            }
            $combined = collect();
            if ($showAddition) {
                $combined->push($addition);
            }
            $combined = $combined->concat($usages);
        } else {
            $combined = $additions->concat($usages);
        }

        $history = $combined->sortByDesc('tanggal_raw')->values();

        return view('stock-amprahan.history-print', [
            'item' => $item,
            'history' => $history,
        ]);
    }

    public function exportHistoryExcel(Request $request)
    {
        $filters = [
            'id' => $request->id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'lokasi' => $request->lokasi,
            'mobil_id' => $request->mobil_id,
        ];

        $fileName = 'Riwayat_Stock_Amprahan_'.date('Ymd_His').'.xlsx';

        return Excel::download(new StockAmprahanHistoryExport($filters), $fileName);
    }

    public function generateNomorPranota()
    {
        try {
            $kode = 'PTP'; // Pranota Transfer Pelabuhan/Pergudangan/Perlengkapan? (User requested PTP)
            $bulan = now()->format('m');
            $tahun = now()->format('y');

            $prefix = "{$kode}-{$bulan}-{$tahun}-";
            $lastPranota = \App\Models\PranotaStock::where('nomor_pranota', 'like', $prefix.'%')
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
                'nomor_pranota' => $nomorPranota,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor pranota: '.$e->getMessage(),
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
                'bank' => 'nullable|string',
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
                'bank' => $data['bank'],
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
                'redirect' => route('pranota-stock.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memasukkan ke pranota: '.$e->getMessage(),
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

        if ($request->filled('penerima')) {
            $query->where('penerima', 'like', '%'.$request->penerima.'%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('tanggal_pranota', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('tanggal_pranota', '<=', $request->to_date);
        }

        $items = $query->paginate(20)->withQueryString();

        return view('pranota-stock.index', compact('items'));
    }

    public function pranotaPrint($id)
    {
        $pranota = \App\Models\PranotaStock::with('creator')->findOrFail($id);

        // Hydrate items with fresh data from DB to ensure no empty columns
        if (is_array($pranota->items)) {
            $itemIds = collect($pranota->items)->pluck('id')->filter()->toArray();
            $stockItems = \App\Models\StockAmprahan::with(['usages.kendaraan', 'usages.truck', 'usages.buntut', 'usages.chasisBatam', 'usages.kapal', 'usages.alatBerat', 'masterNamaBarangAmprahan'])
                ->whereIn('id', $itemIds)
                ->get()
                ->keyBy('id');

            $hydratedItems = array_map(function ($it) use ($stockItems) {
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
                            $refItems[] = $firstUsage->alatBerat->nama.($firstUsage->alatBerat->warna ? ' - '.$firstUsage->alatBerat->warna : '');
                            if (! $refType) {
                                $refType = 'Alat Berat';
                            }
                        }
                        if ($firstUsage->kendaraan) {
                            $refItems[] = $firstUsage->kendaraan->nomor_polisi;
                            if (! $refType) {
                                $refType = 'Kendaraan';
                            }
                        }
                        if ($firstUsage->truck) {
                            $refItems[] = 'Truck: '.$firstUsage->truck->nomor_polisi;
                            if (! $refType) {
                                $refType = 'Truck';
                            }
                        }
                        if ($firstUsage->chasisBatam) {
                            $refItems[] = 'Buntut: '.$firstUsage->chasisBatam->kode;
                            if (! $refType) {
                                $refType = 'Buntut';
                            }
                        }
                        if ($firstUsage->buntut) {
                            $refItems[] = 'Buntut: '.($firstUsage->buntut->no_kir ?: $firstUsage->buntut->nomor_polisi);
                            if (! $refType) {
                                $refType = 'Buntut';
                            }
                        }
                        if ($firstUsage->kantor) {
                            $refItems[] = $firstUsage->kantor;
                            if (! $refType) {
                                $refType = 'Kantor';
                            }
                        }

                    }
                    $reference = count($refItems) > 0 ? implode(' / ', $refItems) : 'Stock '.($item->lokasi ?? '-');

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
                        'lokasi' => $item->lokasi ?? '-',
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
            return redirect()->back()->with('error', 'Gagal menghapus pranota: '.$e->getMessage());
        }
    }

    public function pranotaEdit($id)
    {
        $pranota = \App\Models\PranotaStock::findOrFail($id);

        // Hydrate items with fresh data from DB to ensure they are up to date
        if (is_array($pranota->items)) {
            $itemIds = collect($pranota->items)->pluck('id')->filter()->toArray();
            $stockItems = \App\Models\StockAmprahan::with(['masterNamaBarangAmprahan'])
                ->whereIn('id', $itemIds)
                ->get()
                ->keyBy('id');

            $hydratedItems = array_map(function ($it) use ($stockItems) {
                $id = $it['id'] ?? null;
                if ($id && isset($stockItems[$id])) {
                    $item = $stockItems[$id];

                    return array_merge($it, [
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

        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();

        $banks = Bank::orderBy('name')->pluck('name')->toArray();

        return view('pranota-stock.edit', compact('pranota', 'karyawans', 'banks'));
    }

    public function pranotaUpdate(Request $request, $id)
    {
        try {
            $pranota = \App\Models\PranotaStock::findOrFail($id);

            $data = $request->validate([
                'nomor_pranota' => 'required|string|unique:pranota_stocks,nomor_pranota,'.$id,
                'tanggal_pranota' => 'required|date',
                'nomor_accurate' => 'nullable|string',
                'vendor' => 'nullable|string',
                'bank' => 'nullable|string',
                'rekening' => 'nullable|string',
                'penerima' => 'nullable|string',
                'adjustment' => 'nullable|numeric',
                'keterangan' => 'nullable|string',
                'items' => 'required|array',
            ]);

            // Track old items to revert status if removed
            $oldItemIds = is_array($pranota->items) ? collect($pranota->items)->pluck('id')->filter()->toArray() : [];
            $newItemIds = collect($data['items'])->pluck('id')->filter()->toArray();

            $removedIds = array_diff($oldItemIds, $newItemIds);

            DB::beginTransaction();

            $pranota->update([
                'nomor_pranota' => $data['nomor_pranota'],
                'tanggal_pranota' => $data['tanggal_pranota'],
                'nomor_accurate' => $data['nomor_accurate'],
                'vendor' => $data['vendor'],
                'bank' => $data['bank'],
                'rekening' => $data['rekening'],
                'penerima' => $data['penerima'],
                'adjustment' => $data['adjustment'] ?? 0,
                'keterangan' => $data['keterangan'],
                'items' => $data['items'],
                'updated_by' => Auth::id(),
            ]);

            // Revert status for removed items
            if (! empty($removedIds)) {
                \App\Models\StockAmprahan::whereIn('id', $removedIds)->update(['status_pranota' => 'Belum']);
            }

            // Ensure status for current items is 'Sudah'
            if (! empty($newItemIds)) {
                \App\Models\StockAmprahan::whereIn('id', $newItemIds)->update(['status_pranota' => 'Sudah']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui pranota',
                'redirect' => route('pranota-stock.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pranota: '.$e->getMessage(),
            ], 500);
        }
    }

    public function pranotaSync($id)
    {
        try {
            $pranota = \App\Models\PranotaStock::findOrFail($id);

            if (! is_array($pranota->items)) {
                return redirect()->back()->with('error', 'Item pranota tidak valid');
            }

            $itemIds = collect($pranota->items)->pluck('id')->filter()->toArray();
            $stockItems = \App\Models\StockAmprahan::with(['masterNamaBarangAmprahan', 'usages'])
                ->whereIn('id', $itemIds)
                ->get()
                ->keyBy('id');

            $updatedItems = array_map(function ($it) use ($stockItems) {
                $id = $it['id'] ?? null;
                if ($id && isset($stockItems[$id])) {
                    $item = $stockItems[$id];

                    // We sync basic info that might have changed
                    // Note: 'jumlah' is tricky because StockAmprahan->jumlah is 'sisa stock'
                    // but many pranotas are made for the TOTAL purchase.
                    // If this item has direct usages, we should probably add them back to get the 'pembelian' amount
                    $totalPurchase = $item->jumlah + $item->usages->sum('jumlah');

                    return array_merge($it, [
                        'nama_barang' => $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? ($it['nama_barang'] ?? '-')),
                        'harga' => $item->harga_satuan ?? ($it['harga'] ?? 0),
                        'adjustment' => $item->adjustment ?? ($it['adjustment'] ?? 0),
                        'satuan' => $item->satuan ?? ($it['satuan'] ?? '-'),
                        'jumlah' => $totalPurchase, // Sync total purchase quantity
                    ]);
                }

                return $it;
            }, $pranota->items);

            $pranota->update([
                'items' => $updatedItems,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('pranota-stock.index')->with('success', 'Data pranota berhasil diperbarui sesuai data stock terbaru.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    public function valuasiPrint(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        $namaBarang = $request->nama_barang;
        $masterItem = (object) [
            'id' => '-',
            'nama_barang' => $namaBarang
        ];
        $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
        $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();

        // Saldo Awal (sebelum from_date)
        // Masuk sebelum from_date
        $masukSebelumQuery = \App\Models\StockAmprahan::where('nama_barang', $namaBarang)
            ->where(function ($q) use ($fromDate) {
                $q->whereDate('tanggal_beli', '<', $fromDate)
                    ->orWhere(function ($sq) use ($fromDate) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '<', $fromDate);
                    });
            });

        // Filter based on Karyawan Cabang (Branch)
        $user = Auth::user();
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $masukSebelumQuery->where('lokasi', 'like', '%BATAM%');
            }
        }

        $masukSebelum = $masukSebelumQuery->get();
        $qtyMasukSebelum = 0;
        $nilaiMasukSebelum = 0;

        foreach ($masukSebelum as $masuk) {
            // Karena jumlah di db adalah sisa, kita perlu menghitung total awal (sisa + total_usage)
            $totalUsage = $masuk->usages()->sum('jumlah');
            $initialStock = $masuk->jumlah + $totalUsage;
            $qtyMasukSebelum += $initialStock;
            $nilaiMasukSebelum += ($initialStock * ($masuk->harga_satuan ?? 0));
        }

        // Keluar sebelum from_date
        $keluarSebelumQuery = \App\Models\StockAmprahanUsage::whereHas('stockAmprahan', function ($q) use ($namaBarang) {
            $q->where('nama_barang', $namaBarang);
        })
            ->whereDate('tanggal_pengambilan', '<', $fromDate);

        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $keluarSebelumQuery->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        $keluarSebelum = $keluarSebelumQuery->with('stockAmprahan')->get();
        $qtyKeluarSebelum = 0;
        $nilaiKeluarSebelum = 0;

        foreach ($keluarSebelum as $keluar) {
            $qtyKeluarSebelum += $keluar->jumlah;
            $nilaiKeluarSebelum += ($keluar->jumlah * ($keluar->stockAmprahan->harga_satuan ?? 0));
        }

        $saldoAwalQty = $qtyMasukSebelum - $qtyKeluarSebelum;
        $saldoAwalNilai = $nilaiMasukSebelum - $nilaiKeluarSebelum;

        // Transaksi dalam periode
        $additionsQuery = \App\Models\StockAmprahan::where('nama_barang', $namaBarang)
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->where(function ($qq) use ($fromDate, $toDate) {
                    $qq->whereNotNull('tanggal_beli')->whereBetween('tanggal_beli', [$fromDate, $toDate]);
                })->orWhere(function ($sq) use ($fromDate, $toDate) {
                    $sq->whereNull('tanggal_beli')->whereBetween('created_at', [$fromDate, $toDate]);
                });
            });

        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $additionsQuery->where('lokasi', 'like', '%BATAM%');
            }
        }

        $additions = $additionsQuery->get()->map(function ($item) {
            $totalUsage = $item->usages()->sum('jumlah');
            $initialStock = $item->jumlah + $totalUsage;

            return (object) [
                'tanggal' => $item->tanggal_beli ? $item->tanggal_beli->format('d M Y') : $item->created_at->format('d M Y'),
                'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                'tipe' => $item->type_amprahan ?? 'Penerimaan Barang',
                'nama_barang' => $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-'),
                'no_faktur' => $item->nomor_bukti ?? '-',
                'referensi' => $item->vendorAmprahan->nama_toko ?? '-',
                'kts_masuk' => $initialStock,
                'nilai_masuk' => $initialStock * ($item->harga_satuan ?? 0),
                'kts_keluar' => 0,
                'nilai_keluar' => 0,
            ];
        });

        $usagesQuery = \App\Models\StockAmprahanUsage::with('stockAmprahan')
            ->whereHas('stockAmprahan', function ($q) use ($namaBarang) {
                $q->where('nama_barang', $namaBarang);
            })
            ->whereBetween('tanggal_pengambilan', [$fromDate, $toDate]);

        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $usagesQuery->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        $usages = $usagesQuery->get()->map(function ($usage) {
            $noFaktur = '-';
            if ($usage->kendaraan) {
                $noFaktur = $usage->kendaraan->nomor_polisi;
            } elseif ($usage->kapal) {
                $noFaktur = $usage->kapal->nama_kapal;
            } elseif ($usage->alatBerat) {
                $noFaktur = $usage->alatBerat->nama;
            } elseif ($usage->kantor) {
                $noFaktur = $usage->kantor;
            }

            return (object) [
                'tanggal' => \Carbon\Carbon::parse($usage->tanggal_pengambilan)->format('d M Y'),
                'tanggal_raw' => $usage->tanggal_pengambilan,
                'tipe' => 'Pemakaian Bahan Baku',
                'nama_barang' => $usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-'),
                'no_faktur' => '-',
                'referensi' => $noFaktur,
                'kts_masuk' => 0,
                'nilai_masuk' => 0,
                'kts_keluar' => $usage->jumlah,
                'nilai_keluar' => $usage->jumlah * ($usage->stockAmprahan->harga_satuan ?? 0),
            ];
        });

        $transaksi = $additions->concat($usages)->sort(function ($a, $b) {
            $timeA = \Carbon\Carbon::parse($a->tanggal_raw)->timestamp;
            $timeB = \Carbon\Carbon::parse($b->tanggal_raw)->timestamp;

            if ($timeA == $timeB) {
                // Jika waktu sama, dahulukan barang masuk
                $aIsMasuk = $a->kts_masuk > 0 ? 1 : 0;
                $bIsMasuk = $b->kts_masuk > 0 ? 1 : 0;

                return $bIsMasuk <=> $aIsMasuk;
            }

            return $timeA <=> $timeB;
        })->values();

        // Calculate running balance
        $runningQty = $saldoAwalQty;
        $runningNilai = $saldoAwalNilai;

        foreach ($transaksi as $trx) {
            $runningQty += $trx->kts_masuk - $trx->kts_keluar;
            $runningNilai += $trx->nilai_masuk - $trx->nilai_keluar;

            $trx->kuantitas = $runningQty;
            $trx->nilai_akhir = $runningNilai;
        }

        return view('stock-amprahan.valuasi-print', [
            'masterItem' => $masterItem,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'saldoAwalQty' => $saldoAwalQty,
            'saldoAwalNilai' => $saldoAwalNilai,
            'transaksi' => $transaksi,
        ]);
    }

    public function valuasiExcel(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        $namaBarang = $request->nama_barang;
        $masterItem = (object) [
            'id' => '-',
            'nama_barang' => $namaBarang
        ];
        $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
        $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();

        // Saldo Awal (sebelum from_date)
        // Masuk sebelum from_date
        $masukSebelumQuery = \App\Models\StockAmprahan::where('nama_barang', $namaBarang)
            ->where(function ($q) use ($fromDate) {
                $q->whereDate('tanggal_beli', '<', $fromDate)
                    ->orWhere(function ($sq) use ($fromDate) {
                        $sq->whereNull('tanggal_beli')->whereDate('created_at', '<', $fromDate);
                    });
            });

        // Filter based on Karyawan Cabang (Branch)
        $user = Auth::user();
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $masukSebelumQuery->where('lokasi', 'like', '%BATAM%');
            }
        }

        $masukSebelum = $masukSebelumQuery->get();
        $qtyMasukSebelum = 0;
        $nilaiMasukSebelum = 0;

        foreach ($masukSebelum as $masuk) {
            $totalUsage = $masuk->usages()->sum('jumlah');
            $initialStock = $masuk->jumlah + $totalUsage;
            $qtyMasukSebelum += $initialStock;
            $nilaiMasukSebelum += ($initialStock * ($masuk->harga_satuan ?? 0));
        }

        // Keluar sebelum from_date
        $keluarSebelumQuery = \App\Models\StockAmprahanUsage::whereHas('stockAmprahan', function ($q) use ($namaBarang) {
            $q->where('nama_barang', $namaBarang);
        })
            ->whereDate('tanggal_pengambilan', '<', $fromDate);

        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $keluarSebelumQuery->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        $keluarSebelum = $keluarSebelumQuery->with('stockAmprahan')->get();
        $qtyKeluarSebelum = 0;
        $nilaiKeluarSebelum = 0;

        foreach ($keluarSebelum as $keluar) {
            $qtyKeluarSebelum += $keluar->jumlah;
            $nilaiKeluarSebelum += ($keluar->jumlah * ($keluar->stockAmprahan->harga_satuan ?? 0));
        }

        $saldoAwalQty = $qtyMasukSebelum - $qtyKeluarSebelum;
        $saldoAwalNilai = $nilaiMasukSebelum - $nilaiKeluarSebelum;

        // Transaksi dalam periode
        $additionsQuery = \App\Models\StockAmprahan::where('nama_barang', $namaBarang)
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->where(function ($qq) use ($fromDate, $toDate) {
                    $qq->whereNotNull('tanggal_beli')->whereBetween('tanggal_beli', [$fromDate, $toDate]);
                })->orWhere(function ($sq) use ($fromDate, $toDate) {
                    $sq->whereNull('tanggal_beli')->whereBetween('created_at', [$fromDate, $toDate]);
                });
            });

        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $additionsQuery->where('lokasi', 'like', '%BATAM%');
            }
        }

        $additions = $additionsQuery->get()->map(function ($item) {
            $totalUsage = $item->usages()->sum('jumlah');
            $initialStock = $item->jumlah + $totalUsage;

            return (object) [
                'tanggal' => $item->tanggal_beli ? $item->tanggal_beli->format('d M Y') : $item->created_at->format('d M Y'),
                'tanggal_raw' => $item->tanggal_beli ?? $item->created_at,
                'tipe' => $item->type_amprahan ?? 'Penerimaan Barang',
                'nama_barang' => $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '-'),
                'no_faktur' => $item->nomor_bukti ?? '-',
                'referensi' => $item->vendorAmprahan->nama_toko ?? '-',
                'kts_masuk' => $initialStock,
                'nilai_masuk' => $initialStock * ($item->harga_satuan ?? 0),
                'kts_keluar' => 0,
                'nilai_keluar' => 0,
            ];
        });

        $usagesQuery = \App\Models\StockAmprahanUsage::with('stockAmprahan')
            ->whereHas('stockAmprahan', function ($q) use ($namaBarang) {
                $q->where('nama_barang', $namaBarang);
            })
            ->whereBetween('tanggal_pengambilan', [$fromDate, $toDate]);

        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $usagesQuery->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        $usages = $usagesQuery->get()->map(function ($usage) {
            $noFaktur = '-';
            if ($usage->kendaraan) {
                $noFaktur = $usage->kendaraan->nomor_polisi;
            } elseif ($usage->kapal) {
                $noFaktur = $usage->kapal->nama_kapal;
            } elseif ($usage->alatBerat) {
                $noFaktur = $usage->alatBerat->nama;
            } elseif ($usage->kantor) {
                $noFaktur = $usage->kantor;
            }

            return (object) [
                'tanggal' => \Carbon\Carbon::parse($usage->tanggal_pengambilan)->format('d M Y'),
                'tanggal_raw' => $usage->tanggal_pengambilan,
                'tipe' => 'Pemakaian Bahan Baku',
                'nama_barang' => $usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-'),
                'no_faktur' => '-',
                'referensi' => $noFaktur,
                'kts_masuk' => 0,
                'nilai_masuk' => 0,
                'kts_keluar' => $usage->jumlah,
                'nilai_keluar' => $usage->jumlah * ($usage->stockAmprahan->harga_satuan ?? 0),
            ];
        });

        $transaksi = $additions->concat($usages)->sort(function ($a, $b) {
            $timeA = \Carbon\Carbon::parse($a->tanggal_raw)->timestamp;
            $timeB = \Carbon\Carbon::parse($b->tanggal_raw)->timestamp;

            if ($timeA == $timeB) {
                $aIsMasuk = $a->kts_masuk > 0 ? 1 : 0;
                $bIsMasuk = $b->kts_masuk > 0 ? 1 : 0;

                return $bIsMasuk <=> $aIsMasuk;
            }

            return $timeA <=> $timeB;
        })->values();

        $runningQty = $saldoAwalQty;
        $runningNilai = $saldoAwalNilai;

        foreach ($transaksi as $trx) {
            $runningQty += $trx->kts_masuk - $trx->kts_keluar;
            $runningNilai += $trx->nilai_masuk - $trx->nilai_keluar;

            $trx->kuantitas = $runningQty;
            $trx->nilai_akhir = $runningNilai;
        }

        $data = [
            'masterItem' => $masterItem,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'saldoAwalQty' => $saldoAwalQty,
            'saldoAwalNilai' => $saldoAwalNilai,
            'transaksi' => $transaksi,
        ];

        $fileName = 'Valuasi_Persediaan_'.str_replace(' ', '_', $masterItem->nama_barang).'_'.date('Ymd_His').'.xlsx';

        return Excel::download(new \App\Exports\ValuasiPersediaanExport($data), $fileName);
    }

    public function valuasiPemakaianPrint(Request $request)
    {
        $request->validate([
            'kategori_pemakai' => 'required|in:penerima,kendaraan,alat_berat,kapal,kantor',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'penerima_id' => 'required_if:kategori_pemakai,penerima|nullable|exists:karyawans,id',
            'kendaraan_id' => 'required_if:kategori_pemakai,kendaraan|nullable|exists:mobils,id',
            'alat_berat_id' => 'required_if:kategori_pemakai,alat_berat|nullable|exists:alat_berats,id',
            'kapal_id' => 'required_if:kategori_pemakai,kapal|nullable|exists:master_kapals,id',
            'kantor' => 'required_if:kategori_pemakai,kantor|nullable|string',
        ]);

        $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
        $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
        $kategori = $request->kategori_pemakai;
        $pemakaiName = '';

        $query = \App\Models\StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'stockAmprahan.vendorAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'kapal', 'alatBerat'])
            ->whereBetween('tanggal_pengambilan', [$fromDate, $toDate]);

        // Filter by pemakai
        if ($kategori === 'penerima') {
            $penerima = \App\Models\Karyawan::findOrFail($request->penerima_id);
            $pemakaiName = $penerima->nama_lengkap;
            $query->where('penerima_id', $request->penerima_id);
        } elseif ($kategori === 'kendaraan') {
            $mobil = \App\Models\Mobil::findOrFail($request->kendaraan_id);
            $pemakaiName = $mobil->nomor_polisi.($mobil->merek ? ' - '.$mobil->merek : '');
            $query->where(function ($q) use ($request) {
                $q->where('kendaraan_id', $request->kendaraan_id)
                    ->orWhere('truck_id', $request->kendaraan_id)
                    ->orWhere('buntut_id', $request->kendaraan_id);
            });
        } elseif ($kategori === 'alat_berat') {
            $alat = \App\Models\AlatBerat::findOrFail($request->alat_berat_id);
            $pemakaiName = $alat->kode_alat.' - '.$alat->nama;
            $query->where('alat_berat_id', $request->alat_berat_id);
        } elseif ($kategori === 'kapal') {
            $kapal = \App\Models\MasterKapal::findOrFail($request->kapal_id);
            $pemakaiName = $kapal->nama_kapal;
            $query->where('kapal_id', $request->kapal_id);
        } elseif ($kategori === 'kantor') {
            $pemakaiName = $request->kantor;
            $query->where('kantor', $request->kantor);
        }

        // Filter based on Karyawan Cabang (Branch)
        $user = Auth::user();
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $query->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        $usages = $query->orderBy('tanggal_pengambilan', 'asc')->get();

        return view('stock-amprahan.valuasi-pemakaian-print', [
            'kategori' => ucfirst($kategori === 'penerima' ? 'Karyawan / Penerima' : ($kategori === 'kendaraan' ? 'Kendaraan / Truck' : str_replace('_', ' ', $kategori))),
            'pemakaiName' => $pemakaiName,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'usages' => $usages,
        ]);
    }

    public function valuasiPemakaianExcel(Request $request)
    {
        $request->validate([
            'kategori_pemakai' => 'required|in:penerima,kendaraan,alat_berat,kapal,kantor',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'penerima_id' => 'required_if:kategori_pemakai,penerima|nullable|exists:karyawans,id',
            'kendaraan_id' => 'required_if:kategori_pemakai,kendaraan|nullable|exists:mobils,id',
            'alat_berat_id' => 'required_if:kategori_pemakai,alat_berat|nullable|exists:alat_berats,id',
            'kapal_id' => 'required_if:kategori_pemakai,kapal|nullable|exists:master_kapals,id',
            'kantor' => 'required_if:kategori_pemakai,kantor|nullable|string',
        ]);

        $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
        $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
        $kategori = $request->kategori_pemakai;
        $pemakaiName = '';

        $query = \App\Models\StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'stockAmprahan.vendorAmprahan', 'penerima', 'kendaraan', 'truck', 'buntut', 'kapal', 'alatBerat'])
            ->whereBetween('tanggal_pengambilan', [$fromDate, $toDate]);

        // Filter by pemakai
        if ($kategori === 'penerima') {
            $penerima = \App\Models\Karyawan::findOrFail($request->penerima_id);
            $pemakaiName = $penerima->nama_lengkap;
            $query->where('penerima_id', $request->penerima_id);
        } elseif ($kategori === 'kendaraan') {
            $mobil = \App\Models\Mobil::findOrFail($request->kendaraan_id);
            $pemakaiName = $mobil->nomor_polisi.($mobil->merek ? ' - '.$mobil->merek : '');
            $query->where(function ($q) use ($request) {
                $q->where('kendaraan_id', $request->kendaraan_id)
                    ->orWhere('truck_id', $request->kendaraan_id)
                    ->orWhere('buntut_id', $request->kendaraan_id);
            });
        } elseif ($kategori === 'alat_berat') {
            $alat = \App\Models\AlatBerat::findOrFail($request->alat_berat_id);
            $pemakaiName = $alat->kode_alat.' - '.$alat->nama;
            $query->where('alat_berat_id', $request->alat_berat_id);
        } elseif ($kategori === 'kapal') {
            $kapal = \App\Models\MasterKapal::findOrFail($request->kapal_id);
            $pemakaiName = $kapal->nama_kapal;
            $query->where('kapal_id', $request->kapal_id);
        } elseif ($kategori === 'kantor') {
            $pemakaiName = $request->kantor;
            $query->where('kantor', $request->kantor);
        }

        // Filter based on Karyawan Cabang (Branch)
        $user = Auth::user();
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $query->whereHas('stockAmprahan', function ($q) {
                    $q->where('lokasi', 'like', '%BATAM%');
                });
            }
        }

        $usages = $query->orderBy('tanggal_pengambilan', 'asc')->get();

        $data = [
            'kategori' => ucfirst($kategori === 'penerima' ? 'Karyawan / Penerima' : ($kategori === 'kendaraan' ? 'Kendaraan / Truck' : str_replace('_', ' ', $kategori))),
            'pemakaiName' => $pemakaiName,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'usages' => $usages,
        ];

        $fileName = 'Valuasi_Pemakaian_'.str_replace(' ', '_', $pemakaiName).'_'.date('Ymd_His').'.xlsx';

        return Excel::download(new \App\Exports\ValuasiPemakaianExport($data), $fileName);
    }

    public function valuasiPembelianPrint(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'lokasi' => 'nullable|string',
        ]);

        $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
        $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
        $lokasi = $request->lokasi;
        $lokasiName = 'Semua Lokasi';

        $query = \App\Models\StockAmprahan::with(['masterNamaBarangAmprahan', 'vendorAmprahan', 'usages'])
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('tanggal_beli', [$fromDate, $toDate])
                    ->orWhere(function ($sq) use ($fromDate, $toDate) {
                        $sq->whereNull('tanggal_beli')->whereBetween('created_at', [$fromDate, $toDate]);
                    });
            });

        if ($lokasi) {
            $lokasiName = $lokasi;
            if ($lokasi === 'LAINNYA') {
                $query->where(function ($q) {
                    $q->whereNotIn('lokasi', ['KANTOR AYP JAKARTA', 'KANTOR AYP BATAM'])
                        ->orWhereNull('lokasi');
                });
            } else {
                $query->where('lokasi', $lokasi);
            }
        }

        // Filter based on Karyawan Cabang (Branch)
        $user = Auth::user();
        if ($user && $user->karyawan && ! empty($user->karyawan->cabang) && ! $user->hasRole('Super Admin') && ! $user->hasRole('Admin')) {
            $cabang = strtoupper($user->karyawan->cabang);
            if ($cabang === 'BATAM') {
                $query->where('lokasi', 'like', '%BATAM%');
            }
        }

        $purchases = $query->orderBy('tanggal_beli', 'asc')->orderBy('created_at', 'asc')->get();

        return view('stock-amprahan.valuasi-pembelian-print', [
            'lokasiName' => $lokasiName,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'purchases' => $purchases,
        ]);
    }
}
