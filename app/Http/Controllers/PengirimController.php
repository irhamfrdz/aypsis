<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengirim;
use Illuminate\Http\Request;

class PengirimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengirim::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_pengirim', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $pengirims = $query->paginate(15);

        return view('master-pengirim.index', compact('pengirims'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-pengirim.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:pengirim,kode',
            'nama_pengirim' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Pengirim::create($request->all());

        return redirect()->route('pengirim.index')->with('success', 'Pengirim berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengirim = Pengirim::findOrFail($id);
        return view('master-pengirim.show', compact('pengirim'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengirim = Pengirim::findOrFail($id);
        return view('master-pengirim.edit', compact('pengirim'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengirim = Pengirim::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:pengirim,kode,' . $id,
            'nama_pengirim' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $pengirim->update($request->all());

        return redirect()->route('pengirim.index')->with('success', 'Pengirim berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengirim = Pengirim::findOrFail($id);
        $pengirim->delete();

        return redirect()->route('pengirim.index')->with('success', 'Pengirim berhasil dihapus.');
    }
}
