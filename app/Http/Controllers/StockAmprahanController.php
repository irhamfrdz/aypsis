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

class StockAmprahanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'updatedBy'])
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
        
        $items = $query->paginate(20);
            
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();

        return view('stock-amprahan.index', compact('items', 'karyawans', 'mobils', 'alatBerats', 'kapals', 'search'));
    }

    public function create()
    {
        $masterItems = MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $gudangItems = MasterGudangAmprahan::where('status', 'active')->orderBy('nama_gudang')->get();
        
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();

        return view('stock-amprahan.create', compact('masterItems', 'gudangItems', 'karyawans', 'mobils', 'kapals', 'alatBerats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_bukti' => 'nullable|string|max:255',
            'tanggal_beli' => 'nullable|date',
            'nama_barang' => 'required|string|max:255',
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'harga_satuan' => 'nullable|numeric|min:0',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            
            // Langsung Pakai Fields
            'is_langsung_pakai' => 'nullable',
            'penerima_id' => 'nullable|required_if:is_langsung_pakai,1|exists:karyawans,id',
            'mobil_id' => 'nullable|exists:mobils,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
            'tanggal_pengambilan' => 'nullable|required_if:is_langsung_pakai,1|date',
            'jumlah_pakai' => 'nullable|required_if:is_langsung_pakai,1|numeric|min:0',
            'keterangan_pakai' => 'nullable|required_if:is_langsung_pakai,1|string',
        ]);

        $data['created_by'] = Auth::id();
        
        // Manual validation for mobil/alat_berat if is_langsung_pakai
        if ($request->is_langsung_pakai == '1') {
            if (empty($request->mobil_id) && empty($request->alat_berat_id) && empty($request->kapal_id)) {
                return redirect()->back()->withErrors(['mobil_id' => 'Pilih mobil, kapal, atau alat berat jika langsung pakai.'])->withInput();
            }
            
            if ($request->jumlah_pakai > $request->jumlah) {
                return redirect()->back()->withErrors(['jumlah_pakai' => 'Jumlah pakai tidak boleh lebih besar dari jumlah stock.'])->withInput();
            }
        }

        $stockData = [
            'nomor_bukti' => $data['nomor_bukti'],
            'tanggal_beli' => $data['tanggal_beli'],
            'nama_barang' => $data['nama_barang'],
            'master_nama_barang_amprahan_id' => $data['master_nama_barang_amprahan_id'],
            'harga_satuan' => $data['harga_satuan'],
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
                'mobil_id' => $request->mobil_id,
                'kapal_id' => $request->kapal_id,
                'alat_berat_id' => $request->alat_berat_id,
                'jumlah' => $request->jumlah_pakai,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => $request->keterangan_pakai,
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
        $item = StockAmprahan::findOrFail($id);
        $masterItems = MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $gudangItems = MasterGudangAmprahan::where('status', 'active')->orderBy('nama_gudang')->get();
        return view('stock-amprahan.edit', compact('item', 'masterItems', 'gudangItems'));
    }

    public function update(Request $request, $id)
    {
        $item = StockAmprahan::findOrFail($id);
        
        $data = $request->validate([
            'nomor_bukti' => 'nullable|string|max:255',
            'tanggal_beli' => 'nullable|date',
            'nama_barang' => 'required|string|max:255',
            'master_nama_barang_amprahan_id' => 'required|exists:master_nama_barang_amprahans,id',
            'harga_satuan' => 'nullable|numeric|min:0',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $data['updated_by'] = Auth::id();

        $item->update($data);

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil diperbarui');
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
            'mobil_id' => 'nullable|exists:mobils,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            $mobilId = $request->mobil_id;
            $alatBeratId = $request->alat_berat_id;

            if (empty($mobilId) && empty($alatBeratId)) {
                $validator->errors()->add('mobil_id', 'Pilih mobil atau alat berat.');
                $validator->errors()->add('alat_berat_id', 'Pilih mobil atau alat berat.');
            }

            if (!empty($mobilId) && !empty($alatBeratId)) {
                $validator->errors()->add('mobil_id', 'Pilih salah satu: mobil atau alat berat.');
                $validator->errors()->add('alat_berat_id', 'Pilih salah satu: mobil atau alat berat.');
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
            'mobil_id' => $request->mobil_id,
            'kapal_id' => $request->kapal_id,
            'alat_berat_id' => $request->alat_berat_id,
            'jumlah' => $request->jumlah,
            'tanggal_pengambilan' => $request->tanggal,
            'keterangan' => $request->keterangan,
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
        $item = StockAmprahan::with('masterNamaBarangAmprahan')->findOrFail($id);
        $usages = StockAmprahanUsage::with(['penerima', 'mobil', 'kapal', 'alatBerat', 'createdBy'])
            ->where('stock_amprahan_id', $id)
            ->latest('tanggal_pengambilan')
            ->get();

        if ($request->ajax()) {
            $formattedUsages = $usages->map(function ($usage) {
                $mobilInfo = $usage->mobil ? ($usage->mobil->nomor_polisi . ' - ' . $usage->mobil->merek) : '-';
                $kapalInfo = $usage->kapal ? $usage->kapal->nama_kapal : '-';
                $alatBeratInfo = $usage->alatBerat ? ($usage->alatBerat->kode_alat . ' - ' . $usage->alatBerat->nama) : '-';
                return [
                    'tanggal' => date('d-m-Y', strtotime($usage->tanggal_pengambilan)),
                    'jumlah' => $usage->jumlah,
                    'penerima' => $usage->penerima->nama_lengkap ?? '-',
                    'mobil' => $mobilInfo,
                    'kapal' => $kapalInfo,
                    'alat_berat' => $alatBeratInfo,
                    'keterangan' => $usage->keterangan,
                    'created_by' => $usage->createdBy->name ?? '-',
                ];
            });
            return response()->json($formattedUsages);
        }

        return view('stock-amprahan.history', compact('item', 'usages'));
    }

    public function allHistory()
    {
        $usages = StockAmprahanUsage::with(['stockAmprahan.masterNamaBarangAmprahan', 'penerima', 'mobil', 'kapal', 'alatBerat', 'createdBy'])
            ->latest('tanggal_pengambilan')
            ->paginate(20);

        return view('stock-amprahan.history', compact('usages'));
    }
}
