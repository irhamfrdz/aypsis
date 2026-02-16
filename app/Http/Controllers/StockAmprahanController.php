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

class StockAmprahanController extends Controller
{
    public function index()
    {
        $items = StockAmprahan::with(['masterNamaBarangAmprahan', 'createdBy', 'updatedBy'])
            ->latest()
            ->paginate(20);
            
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $alatBerats = AlatBerat::orderBy('kode_alat')->get();
        $kapals = MasterKapal::aktif()->orderBy('nama_kapal')->get();

        return view('stock-amprahan.index', compact('items', 'karyawans', 'mobils', 'alatBerats', 'kapals'));
    }

    public function create()
    {
        $masterItems = MasterNamaBarangAmprahan::where('status', 'active')->orderBy('nama_barang')->get();
        $gudangItems = MasterGudangAmprahan::where('status', 'active')->orderBy('nama_gudang')->get();
        return view('stock-amprahan.create', compact('masterItems', 'gudangItems'));
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
        ]);

        $data['created_by'] = Auth::id();
        
        StockAmprahan::create($data);

        return redirect()->route('stock-amprahan.index')->with('success', 'Stock amprahan berhasil ditambahkan');
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

        // Parse mobil_id
        $requestData = $request->all();
        $alatBeratId = null;
        $mobilId = null;
        if (isset($requestData['mobil_id'])) {
            if (str_starts_with($requestData['mobil_id'], 'alat-')) {
                $alatBeratId = substr($requestData['mobil_id'], 5);
                $requestData['alat_berat_id'] = $alatBeratId;
                $requestData['mobil_id'] = null;
            } elseif (str_starts_with($requestData['mobil_id'], 'mobil-')) {
                $mobilId = substr($requestData['mobil_id'], 6);
                $requestData['mobil_id'] = $mobilId;
            }
        }

        $validator = \Validator::make($requestData, [
            'jumlah' => 'required|numeric|min:0.01|max:' . $item->jumlah,
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'penerima_id' => 'required|exists:karyawans,id',
            'mobil_id' => 'nullable|exists:mobils,id',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'alat_berat_id' => 'nullable|exists:alat_berats,id',
        ]);

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
            'mobil_id' => $requestData['mobil_id'],
            'kapal_id' => $requestData['kapal_id'],
            'alat_berat_id' => $requestData['alat_berat_id'],
            'jumlah' => $request->jumlah,
            'tanggal_pengambilan' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengambilan barang berhasil dicatat. Sisa stock: ' . $item->jumlah . ' ' . $item->satuan,
                'redirect' => route('stock-amprahan.index')
            ]);
        }

        return redirect()->route('stock-amprahan.index')
            ->with('success', 'Pengambilan barang berhasil dicatat. Sisa stock: ' . $item->jumlah . ' ' . $item->satuan);
    }
    public function history(Request $request, $id)
    {
        $item = StockAmprahan::with('masterNamaBarangAmprahan')->findOrFail($id);
        $usages = StockAmprahanUsage::with(['penerima', 'mobil', 'kapal', 'createdBy'])
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
