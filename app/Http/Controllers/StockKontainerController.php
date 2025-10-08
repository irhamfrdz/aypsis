<?php

namespace App\Http\Controllers;

use App\Models\StockKontainer;
use Illuminate\Http\Request;

class StockKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockKontainer::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }



        // Search berdasarkan nomor kontainer
        if ($request->filled('search')) {
            $query->where('nomor_kontainer', 'like', '%' . $request->search . '%');
        }

        $stockKontainers = $query->latest()->paginate(15);

        return view('master-stock-kontainer.index', compact('stockKontainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-stock-kontainer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'ukuran' => 'nullable|string|in:20ft,40ft',
            'tipe_kontainer' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance,damaged,inactive',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        // Gabungkan nomor seri
        $nomorSeriGabungan = $request->awalan_kontainer . $request->nomor_seri_kontainer . $request->akhiran_kontainer;

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer
        $existingWithSameSerialAndSuffix = StockKontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set stock kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);

            $warningMessage = "Stock kontainer dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Stock kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

        // Validasi unique untuk nomor seri gabungan di stock_kontainers (sebagai backup)
        $existingStock = StockKontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)
            ->where('id', '!=', $stockKontainer->id ?? null)
            ->first();
        if ($existingStock && $existingStock->status === 'active') {
            return back()->withErrors(['nomor_seri_gabungan' => 'Nomor kontainer sudah ada di stock kontainer aktif.'])->withInput();
        }

        // Cek apakah nomor kontainer sudah ada di tabel kontainers
        $existingKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)->first();
        $status = $request->status;

        if ($existingKontainer && $existingKontainer->status === 'active') {
            // Jika ada duplikasi dengan kontainer aktif, set status menjadi inactive
            $status = 'inactive';
            session()->flash('warning', 'Nomor kontainer sudah ada di master kontainer aktif. Status diset menjadi inactive untuk menghindari duplikasi.');
        }

        $data = $request->all();
        $data['nomor_seri_gabungan'] = $nomorSeriGabungan;
        $data['status'] = $status;

        StockKontainer::create($data);

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockKontainer $stockKontainer)
    {
        return view('master-stock-kontainer.show', compact('stockKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockKontainer $stockKontainer)
    {
        return view('master-stock-kontainer.edit', compact('stockKontainer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockKontainer $stockKontainer)
    {
        $request->validate([
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'ukuran' => 'nullable|string|in:20ft,40ft',
            'tipe_kontainer' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance,damaged,inactive',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        // Gabungkan nomor seri
        $nomorSeriGabungan = $request->awalan_kontainer . $request->nomor_seri_kontainer . $request->akhiran_kontainer;

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer (selain diri sendiri)
        $existingWithSameSerialAndSuffix = StockKontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->where('id', '!=', $stockKontainer->id)
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set stock kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);

            $warningMessage = "Stock kontainer lain dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Stock kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

        // Validasi unique untuk nomor seri gabungan (kecuali untuk record yang sedang diupdate)
        $existingStock = StockKontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)
                                      ->where('id', '!=', $stockKontainer->id)
                                      ->where('status', 'active')
                                      ->first();
        if ($existingStock) {
            return back()->withErrors(['nomor_seri_gabungan' => 'Nomor kontainer sudah ada di stock kontainer aktif.'])->withInput();
        }

        // Cek apakah nomor kontainer sudah ada di tabel kontainers
        $existingKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)->first();
        $status = $request->status;

        if ($existingKontainer && $existingKontainer->status === 'active' && $request->status !== 'inactive') {
            // Jika ada duplikasi dengan kontainer aktif dan user tidak sengaja memilih inactive, paksa jadi inactive
            $status = 'inactive';
            session()->flash('warning', 'Nomor kontainer sudah ada di master kontainer aktif. Status diset menjadi inactive untuk menghindari duplikasi.');
        }

        $data = $request->all();
        $data['nomor_seri_gabungan'] = $nomorSeriGabungan;
        $data['status'] = $status;

        $stockKontainer->update($data);

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockKontainer $stockKontainer)
    {
        $stockKontainer->delete();

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil dihapus.');
    }
}
