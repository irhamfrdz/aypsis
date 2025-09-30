<?php

namespace App\Http\Controllers;

use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NomorTerakhirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NomorTerakhir::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('modul', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
        }

        $nomorTerakhirs = $query->orderBy('modul')->paginate(15);

        return view('master.nomor-terakhir.index', compact('nomorTerakhirs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.nomor-terakhir.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'modul' => 'required|string|max:100|unique:nomor_terakhir,modul',
            'nomor_terakhir' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        // Convert nomor_terakhir to integer to handle leading zeros
        $data = $request->all();
        $data['nomor_terakhir'] = (int) $data['nomor_terakhir'];

        NomorTerakhir::create($data);

        return redirect()->route('master.nomor-terakhir.index')
                        ->with('success', 'Nomor Terakhir berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NomorTerakhir $nomorTerakhir)
    {
        return view('master.nomor-terakhir.show', compact('nomorTerakhir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NomorTerakhir $nomorTerakhir)
    {
        return view('master.nomor-terakhir.edit', compact('nomorTerakhir'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NomorTerakhir $nomorTerakhir)
    {
        $request->validate([
            'modul' => ['required', 'string', 'max:100', Rule::unique('nomor_terakhir')->ignore($nomorTerakhir->id)],
            'nomor_terakhir' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        // Convert nomor_terakhir to integer to handle leading zeros
        $data = $request->all();
        $data['nomor_terakhir'] = (int) $data['nomor_terakhir'];

        $nomorTerakhir->update($data);

        return redirect()->route('master.nomor-terakhir.index')
                        ->with('success', 'Nomor Terakhir berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NomorTerakhir $nomorTerakhir)
    {
        $nomorTerakhir->delete();

        return redirect()->route('master.nomor-terakhir.index')
                        ->with('success', 'Nomor Terakhir berhasil dihapus.');
    }
}
