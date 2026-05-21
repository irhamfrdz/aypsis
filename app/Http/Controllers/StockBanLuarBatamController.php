<?php

namespace App\Http\Controllers;

use App\Models\MerkBan;
use App\Models\Mobil;
use App\Models\NamaStockBan;
use App\Models\StockBanLuarBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockBanLuarBatamController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $namaStockBans = NamaStockBan::where('status', 'active')->orderBy('nama')->get();
        $merkBans = MerkBan::orderBy('nama')->get();
        $masterGudangBans = \App\Models\MasterGudangBan::where('status', 'aktif')->orderBy('nama_gudang')->get();
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        $nextInvoice = StockBanLuarBatam::generateNextInvoice();

        return view('stock-ban-luar-batam.create', compact('mobils', 'namaStockBans', 'merkBans', 'masterGudangBans', 'karyawans', 'nextInvoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'nullable|unique:stock_ban_luar_batams,nomor_seri',
            'nomor_faktur' => 'nullable|string|max:255',
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required',
            'harga_beli' => 'nullable|numeric|min:0',
            'tempat_beli' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:karyawans,id',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['status'] = 'Stok';
        $data['lokasi'] = $request->lokasi ?? 'Gudang Batam';

        // Handle merk_id from dropdown
        if ($request->filled('merk_id')) {
            $merkBan = MerkBan::find($request->merk_id);
            if ($merkBan) {
                $data['merk'] = $merkBan->nama;
            }
        }

        StockBanLuarBatam::create($data);

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban Luar Batam berhasil ditambahkan')->with('active_tab', 'tab-ban-luar-batam');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stockBan = StockBanLuarBatam::findOrFail($id);
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $namaStockBans = NamaStockBan::where('status', 'active')->orderBy('nama')->get();
        $merkBans = MerkBan::orderBy('nama')->get();
        $masterGudangBans = \App\Models\MasterGudangBan::where('status', 'aktif')->orderBy('nama_gudang')->get();
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();

        return view('stock-ban-luar-batam.edit', compact('stockBan', 'mobils', 'namaStockBans', 'merkBans', 'masterGudangBans', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stockBan = StockBanLuarBatam::findOrFail($id);

        $request->validate([
            'nama_stock_ban_id' => 'required|exists:nama_stock_bans,id',
            'nomor_seri' => 'nullable|unique:stock_ban_luar_batams,nomor_seri,'.$stockBan->id,
            'nomor_faktur' => 'nullable|string|max:255',
            'merk' => 'nullable|required_without:merk_id|string|max:255',
            'merk_id' => 'nullable|exists:merk_bans,id',
            'ukuran' => 'nullable|string|max:255',
            'kondisi' => 'required',
            'harga_beli' => 'nullable|numeric|min:0',
            'tempat_beli' => 'nullable|string|max:255',
            'tanggal_masuk' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'mobil_id' => 'nullable|exists:mobils,id',
            'nomor_bukti' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:karyawans,id',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        // Handle merk_id from dropdown
        if ($request->filled('merk_id')) {
            $merkBan = MerkBan::find($request->merk_id);
            if ($merkBan) {
                $data['merk'] = $merkBan->nama;
            }
        }

        $stockBan->update($data);

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban Luar Batam berhasil diperbarui')->with('active_tab', 'tab-ban-luar-batam');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stockBan = StockBanLuarBatam::findOrFail($id);
        $stockBan->delete();

        return redirect()->route('stock-ban.index')->with('success', 'Data Stock Ban Luar Batam berhasil dihapus')->with('active_tab', 'tab-ban-luar-batam');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stockBan = StockBanLuarBatam::with(['namaStockBan', 'mobil', 'penerima', 'createdBy', 'updatedBy'])->findOrFail($id);

        return view('stock-ban-luar-batam.show', compact('stockBan'));
    }

    /**
     * Mark Batam ban as lost.
     */
    public function hilang(Request $request, $id)
    {
        $stockBan = StockBanLuarBatam::findOrFail($id);

        if ($stockBan->status === 'Dijual' || $stockBan->status === 'Dikembalikan' || $stockBan->status === 'Hilang') {
            return redirect()->route('stock-ban.index')->with('error', 'Ban ini tidak bisa ditandai sebagai hilang.')->with('active_tab', 'tab-ban-luar-batam');
        }

        $request->validate([
            'tanggal_hilang' => 'required|date',
            'keterangan_hilang' => 'nullable|string',
        ]);

        $tanggalHilang = \Carbon\Carbon::parse($request->tanggal_hilang)->format('d-m-Y');

        $lostNote = '[Hilang] Tgl: '.$tanggalHilang;
        if ($request->filled('keterangan_hilang')) {
            $lostNote .= ', Ket: '.$request->keterangan_hilang;
        }

        $stockBan->update([
            'status' => 'Hilang',
            'lokasi' => 'Hilang',
            'keterangan' => $stockBan->keterangan ? ($stockBan->keterangan."\n".$lostNote) : $lostNote,
            'mobil_id' => null,
            'alat_berat_id' => null,
            'penerima_id' => null,
            'kapal_id' => null,
            'tanggal_keluar' => null,
            'tanggal_kirim' => null,
        ]);

        return redirect()->route('stock-ban.index')->with('success', 'Ban Batam berhasil ditandai sebagai hilang.')->with('active_tab', 'tab-ban-luar-batam');
    }
}
