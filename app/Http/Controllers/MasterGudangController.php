<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class MasterGudangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gudang::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_gudang', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $gudangs = $query->orderBy('nama_gudang', 'asc')->paginate(15);

        return view('master-gudang.index', compact('gudangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-gudang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        Gudang::create($validated);

        return redirect()->route('master-gudang.index')
            ->with('success', 'Data gudang berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gudang $masterGudang)
    {
        return view('master-gudang.show', compact('masterGudang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gudang $masterGudang)
    {
        return view('master-gudang.edit', compact('masterGudang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gudang $masterGudang)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $masterGudang->update($validated);

        return redirect()->route('master-gudang.index')
            ->with('success', 'Data gudang berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gudang $masterGudang)
    {
        $masterGudang->delete();

        return redirect()->route('master-gudang.index')
            ->with('success', 'Data gudang berhasil dihapus');
    }
}
