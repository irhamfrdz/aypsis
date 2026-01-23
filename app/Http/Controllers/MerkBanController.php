<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MerkBan;
use Illuminate\Http\Request;

class MerkBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MerkBan::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('nama', 'LIKE', '%' . $searchTerm . '%');
        }

        $merkBans = $query->paginate(15);

        return view('master-merk-ban.index', compact('merkBans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-merk-ban.create');
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

        MerkBan::create($request->all());

        return redirect()->route('master.merk-ban.index')->with('success', 'Merk ban berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $merkBan = MerkBan::findOrFail($id);
        return view('master-merk-ban.show', compact('merkBan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $merkBan = MerkBan::findOrFail($id);
        return view('master-merk-ban.edit', compact('merkBan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $merkBan = MerkBan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $merkBan->update($request->all());

        return redirect()->route('master.merk-ban.index')->with('success', 'Merk ban berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $merkBan = MerkBan::findOrFail($id);
        $merkBan->delete();

        return redirect()->route('master.merk-ban.index')->with('success', 'Merk ban berhasil dihapus.');
    }
}
