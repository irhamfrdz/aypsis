<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NamaStockBan;
use Illuminate\Http\Request;

class NamaStockBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NamaStockBan::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('nama', 'LIKE', '%' . $searchTerm . '%');
        }

        $namaStockBans = $query->paginate(15);

        return view('master-nama-stock-ban.index', compact('namaStockBans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-nama-stock-ban.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        NamaStockBan::create($request->all());

        return redirect()->route('master.nama-stock-ban.index')->with('success', 'Nama stock ban berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $namaStockBan = NamaStockBan::findOrFail($id);
        return view('master-nama-stock-ban.show', compact('namaStockBan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $namaStockBan = NamaStockBan::findOrFail($id);
        return view('master-nama-stock-ban.edit', compact('namaStockBan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $namaStockBan = NamaStockBan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $namaStockBan->update($request->all());

        return redirect()->route('master.nama-stock-ban.index')->with('success', 'Nama stock ban berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $namaStockBan = NamaStockBan::findOrFail($id);
        $namaStockBan->delete();

        return redirect()->route('master.nama-stock-ban.index')->with('success', 'Nama stock ban berhasil dihapus.');
    }
}
