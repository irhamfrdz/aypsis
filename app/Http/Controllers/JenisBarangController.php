<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JenisBarang;
use Illuminate\Http\Request;

class JenisBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JenisBarang::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_barang', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $jenisBarangs = $query->paginate(15);

        return view('master-jenis-barang.index', compact('jenisBarangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-jenis-barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:jenis_barangs,kode',
            'nama_barang' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        JenisBarang::create($request->all());

        return redirect()->route('jenis-barang.index')->with('success', 'Jenis barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);
        return view('master-jenis-barang.show', compact('jenisBarang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);
        return view('master-jenis-barang.edit', compact('jenisBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:jenis_barangs,kode,' . $id,
            'nama_barang' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $jenisBarang->update($request->all());

        return redirect()->route('jenis-barang.index')->with('success', 'Jenis barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);
        $jenisBarang->delete();

        return redirect()->route('jenis-barang.index')->with('success', 'Jenis barang berhasil dihapus.');
    }
}
