<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TipeBarang;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;

class TipeBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipeBarang::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $tipeBarangs = $query->paginate(15);

        return view('master-tipe-barang.index', compact('tipeBarangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-tipe-barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'nullable|string|unique:tipe_barangs,kode',
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();

        if (empty($data['kode'])) {
            $data['kode'] = $this->generateCode();
        }

        TipeBarang::create($data);

        return redirect()->route('master.tipe-barang.index')->with('success', 'Tipe barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipeBarang = TipeBarang::findOrFail($id);
        return view('master-tipe-barang.show', compact('tipeBarang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tipeBarang = TipeBarang::findOrFail($id);
        return view('master-tipe-barang.edit', compact('tipeBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipeBarang = TipeBarang::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:tipe_barangs,kode,' . $id,
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $tipeBarang->update($request->all());

        return redirect()->route('master.tipe-barang.index')->with('success', 'Tipe barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipeBarang = TipeBarang::findOrFail($id);
        $tipeBarang->delete();

        return redirect()->route('master.tipe-barang.index')->with('success', 'Tipe barang berhasil dihapus.');
    }

    private function generateCode()
    {
        $nomorTerakhir = NomorTerakhir::where('modul', 'TB')->first();

        if (!$nomorTerakhir) {
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'TB',
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor terakhir untuk kode tipe barang'
            ]);
        }

        $runningNumber = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->update(['nomor_terakhir' => $runningNumber]);

        return 'TB' . str_pad($runningNumber, 5, '0', STR_PAD_LEFT);
    }
}
