<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterNamaBarangAmprahan;
use Illuminate\Http\Request;
use App\Exports\MasterNamaBarangAmprahanExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterNamaBarangAmprahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterNamaBarangAmprahan::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('nama_barang', 'LIKE', '%' . $searchTerm . '%');
        }

        $barangAmprahans = $query->latest()->paginate(15);

        return view('master-nama-barang-amprahan.index', compact('barangAmprahans'));
    }

    /**
     * Export data to Excel.
     */
    public function export(Request $request)
    {
        $filters = [
            'search' => $request->search
        ];

        return Excel::download(new MasterNamaBarangAmprahanExport($filters), 'master_nama_barang_amprahan_' . date('YmdHis') . '.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-nama-barang-amprahan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:master_nama_barang_amprahans,nama_barang',
            'status' => 'required|in:active,inactive',
        ]);

        MasterNamaBarangAmprahan::create($request->all());

        return redirect()->route('master.nama-barang-amprahan.index')
            ->with('success', 'Barang Amprahan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barangAmprahan = MasterNamaBarangAmprahan::findOrFail($id);
        return view('master-nama-barang-amprahan.edit', compact('barangAmprahan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $barangAmprahan = MasterNamaBarangAmprahan::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:master_nama_barang_amprahans,nama_barang,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        $barangAmprahan->update($request->all());

        return redirect()->route('master.nama-barang-amprahan.index')
            ->with('success', 'Barang Amprahan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barangAmprahan = MasterNamaBarangAmprahan::findOrFail($id);
        $barangAmprahan->delete();

        return redirect()->route('master.nama-barang-amprahan.index')
            ->with('success', 'Barang Amprahan berhasil dihapus.');
    }
}
