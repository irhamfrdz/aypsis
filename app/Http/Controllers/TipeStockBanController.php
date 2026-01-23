<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TipeStockBan;
use Illuminate\Http\Request;

class TipeStockBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipeStockBan::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('nama', 'LIKE', '%' . $searchTerm . '%');
        }

        $tipeStockBans = $query->paginate(15);

        return view('master-tipe-stock-ban.index', compact('tipeStockBans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-tipe-stock-ban.create');
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

        TipeStockBan::create($request->all());

        return redirect()->route('master.tipe-stock-ban.index')->with('success', 'Tipe stock ban berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipeStockBan = TipeStockBan::findOrFail($id);
        return view('master-tipe-stock-ban.show', compact('tipeStockBan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tipeStockBan = TipeStockBan::findOrFail($id);
        return view('master-tipe-stock-ban.edit', compact('tipeStockBan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipeStockBan = TipeStockBan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $tipeStockBan->update($request->all());

        return redirect()->route('master.tipe-stock-ban.index')->with('success', 'Tipe stock ban berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipeStockBan = TipeStockBan::findOrFail($id);
        $tipeStockBan->delete();

        return redirect()->route('master.tipe-stock-ban.index')->with('success', 'Tipe stock ban berhasil dihapus.');
    }
}
