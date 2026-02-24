<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagihanSupirVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Add basic query with pagination and eager loading related suratJalan
        $query = \App\Models\TagihanSupirVendor::with(['suratJalan', 'creator', 'updater']);

        // Handle search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_supir', 'like', '%' . $search . '%')
                  ->orWhere('dari', 'like', '%' . $search . '%')
                  ->orWhere('ke', 'like', '%' . $search . '%')
                  ->orWhere('jenis_kontainer', 'like', '%' . $search . '%')
                  ->orWhereHas('suratJalan', function ($sq) use ($search) {
                      $sq->where('no_surat_jalan', 'like', '%' . $search . '%');
                  });
            });
        }

        // Handle status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status_pembayaran', $request->status);
        }

        // Handle date range filter
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $tagihanSupirVendors = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('tagihan-supir-vendor.index', compact('tagihanSupirVendors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalans,id',
        ]);

        $suratJalanId = $request->surat_jalan_id;

        // Cek apakah sudah ada
        $existing = \App\Models\TagihanSupirVendor::where('surat_jalan_id', $suratJalanId)->exists();
        if ($existing) {
            return redirect()->back()->with('error', 'Tagihan Supir Vendor untuk Surat Jalan ini sudah ada.');
        }

        $suratJalan = \App\Models\SuratJalan::with(['order', 'tujuanPengambilanRelation', 'tujuanPengirimanRelation'])->findOrFail($suratJalanId);

        $dari = $suratJalan->tujuanPengambilanRelation->nama ?? ($suratJalan->order->tujuan_ambil ?? null);
        $ke = $suratJalan->tujuanPengirimanRelation->nama ?? ($suratJalan->order->tujuan_kirim ?? null);

        $pricelist = \App\Models\MasterPricelistVendorSupir::where('ke', $suratJalan->tujuan_pengambilan)
            ->where('jenis_kontainer', $suratJalan->size ?? 20)
            ->where('status', 'aktif')
            ->first();

        $nominal = 0;
        if ($pricelist) {
            $nominal = $pricelist->nominal;
        }

        $tagihan = \App\Models\TagihanSupirVendor::create([
            'surat_jalan_id' => $suratJalan->id,
            'nama_supir' => $suratJalan->supir,
            'dari' => $suratJalan->tujuan_pengambilan ?? $dari,
            'ke' => $suratJalan->tujuan_pengiriman ?? $ke,
            'jenis_kontainer' => $suratJalan->size ?? 20,
            'nominal' => $nominal,
            'status_pembayaran' => 'belum_dibayar',
            'created_by' => \Illuminate\Support\Facades\Auth::id(),
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()->route('tagihan-supir-vendor.edit', $tagihan->id)
            ->with('success', 'Tagihan Supir Vendor berhasil dibuat. Silakan input nominal.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tagihanSupirVendor = \App\Models\TagihanSupirVendor::with(['suratJalan', 'creator', 'updater'])->findOrFail($id);
        
        return view('tagihan-supir-vendor.show', compact('tagihanSupirVendor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tagihanSupirVendor = \App\Models\TagihanSupirVendor::findOrFail($id);
        
        return view('tagihan-supir-vendor.edit', compact('tagihanSupirVendor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric',
            'status_pembayaran' => 'required|in:belum_dibayar,sebagian,lunas',
            'keterangan' => 'nullable|string',
        ]);

        $tagihanSupirVendor = \App\Models\TagihanSupirVendor::findOrFail($id);
        
        $tagihanSupirVendor->update([
            'nominal' => $request->nominal,
            'status_pembayaran' => $request->status_pembayaran,
            'keterangan' => $request->keterangan,
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()->route('tagihan-supir-vendor.index')
            ->with('success', 'Tagihan Supir Vendor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tagihanSupirVendor = \App\Models\TagihanSupirVendor::findOrFail($id);
        $tagihanSupirVendor->delete();

        return redirect()->route('tagihan-supir-vendor.index')
            ->with('success', 'Tagihan Supir Vendor berhasil dihapus.');
    }
}
