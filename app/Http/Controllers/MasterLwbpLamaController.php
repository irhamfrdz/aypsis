<?php

namespace App\Http\Controllers;

use App\Models\MasterLwbpLama;
use Illuminate\Http\Request;

class MasterLwbpLamaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterLwbpLama::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('tahun', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('bulan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $lwbpLamas = $query->orderBy('tahun', 'desc')
                           ->orderBy('bulan', 'desc')
                           ->paginate(15);

        return view('master-lwbp-lama.index', compact('lwbpLamas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-lwbp-lama.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string|max:50',
            'biaya' => 'required|numeric',
            'status' => 'required|string',
        ]);

        MasterLwbpLama::create($request->all());

        return redirect()->route('master.lwbp-lama.index')
            ->with('success', 'Master LWBP Lama berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lwbpLama = MasterLwbpLama::findOrFail($id);
        return view('master-lwbp-lama.show', compact('lwbpLama'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lwbpLama = MasterLwbpLama::findOrFail($id);
        return view('master-lwbp-lama.edit', compact('lwbpLama'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lwbpLama = MasterLwbpLama::findOrFail($id);

        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string|max:50',
            'biaya' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $lwbpLama->update($request->all());

        return redirect()->route('master.lwbp-lama.index')
            ->with('success', 'Master LWBP Lama berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lwbpLama = MasterLwbpLama::findOrFail($id);
        $lwbpLama->delete();

        return redirect()->route('master.lwbp-lama.index')
            ->with('success', 'Master LWBP Lama berhasil dihapus.');
    }
}
