<?php

namespace App\Http\Controllers;

use App\Models\StockAmprahan;
use App\Models\MasterNamaBarangAmprahan;
use App\Models\MasterGudangAmprahan;
use App\Models\StockAmprahanUsage;
use App\Models\Karyawan;
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

        return view('stock-amprahan.index', compact('items', 'karyawans'));
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

        $request->validate([
            'jumlah' => 'required|numeric|min:0.01|max:' . $item->jumlah,
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'penerima_id' => 'required|exists:karyawans,id',
        ]);

        $penerima = Karyawan::findOrFail($request->penerima_id);

        // Decrement stock
        $item->jumlah -= $request->jumlah;
        $item->updated_by = Auth::id();
        $item->save();

        // Create usage record
        StockAmprahanUsage::create([
            'stock_amprahan_id' => $item->id,
            'penerima_id' => $request->penerima_id,
            'jumlah' => $request->jumlah,
            'tanggal_pengambilan' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_by' => Auth::id(),
        ]);
        
        // Append log to keterangan (OPTIONAL: Keep for backward compatibility or remove? Let's remove to avoid clutter)
        // $usageNote = ... 
        // $item->keterangan = ...
        // user requirement is just "feature history", now we have a table. We don't need double storage.


        return redirect()->route('stock-amprahan.index')
            ->with('success', 'Pengambilan barang berhasil dicatat. Sisa stock: ' . $item->jumlah . ' ' . $item->satuan);
    }
    public function history($id)
    {
        $usages = StockAmprahanUsage::with(['penerima', 'createdBy'])
            ->where('stock_amprahan_id', $id)
            ->latest('tanggal_pengambilan')
            ->get()
            ->map(function ($usage) {
                return [
                    'tanggal' => date('d-m-Y', strtotime($usage->tanggal_pengambilan)),
                    'jumlah' => $usage->jumlah,
                    'penerima' => $usage->penerima->nama_lengkap ?? '-',
                    'keterangan' => $usage->keterangan,
                    'created_by' => $usage->createdBy->name ?? '-',
                ];
            });

        return response()->json($usages);
    }
}
