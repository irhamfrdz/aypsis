<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KelolaBbm;
use Illuminate\Http\Request;

class KelolaBbmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = KelolaBbm::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tanggal', 'like', "%{$search}%")
                  ->orWhere('bbm_per_liter', 'like', "%{$search}%")
                  ->orWhere('persentase', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }
        
        $kelolaBbm = $query->orderBy('tanggal', 'desc')->paginate(10);
        
        return view('kelola-bbm.index', compact('kelolaBbm', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelola-bbm.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bbm_per_liter' => 'required|numeric|min:0',
            'persentase' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        KelolaBbm::create($validated);

        return redirect()->route('kelola-bbm.index')
            ->with('success', 'Data BBM berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(KelolaBbm $kelolaBbm)
    {
        return view('kelola-bbm.show', compact('kelolaBbm'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KelolaBbm $kelolaBbm)
    {
        return view('kelola-bbm.edit', compact('kelolaBbm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KelolaBbm $kelolaBbm)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bbm_per_liter' => 'required|numeric|min:0',
            'persentase' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $kelolaBbm->update($validated);

        return redirect()->route('kelola-bbm.index')
            ->with('success', 'Data BBM berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KelolaBbm $kelolaBbm)
    {
        $kelolaBbm->delete();

        return redirect()->route('kelola-bbm.index')
            ->with('success', 'Data BBM berhasil dihapus!');
    }
}
