<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DokumenPerijinanKapal;

class DokumenPerijinanKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dokumens = DokumenPerijinanKapal::latest()->paginate(10);
        return view('master-dokumen-perijinan-kapal.index', compact('dokumens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-dokumen-perijinan-kapal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status_aktif' => 'boolean'
        ]);

        DokumenPerijinanKapal::create($request->all());

        return redirect()->route('master-dokumen-perijinan-kapal.index')
            ->with('success', 'Dokumen perijinan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DokumenPerijinanKapal $dokumenPerijinanKapal)
    {
        // Not used usually for master data, but safe to leave empty or return view
        return view('master-dokumen-perijinan-kapal.show', compact('dokumenPerijinanKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DokumenPerijinanKapal $master_dokumen_perijinan_kapal)
    {
        // Helper variable naming fix because route model binding usually takes the specific parameter name
        // But let's check route: resource uses singular name.
        // I will use $dokumenPerijinanKapal for consistency if I can rename the param, but Laravel matches specific type hint.
        // Actually, let's just use $master_dokumen_perijinan_kapal as per the resource name which is long.
        // Wait, route resource name is 'master-dokumen-perijinan-kapal', so param is likely 'master_dokumen_perijinan_kapal'.
        
        return view('master-dokumen-perijinan-kapal.edit', compact('master_dokumen_perijinan_kapal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DokumenPerijinanKapal $master_dokumen_perijinan_kapal)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status_aktif' => 'boolean'
        ]);

        $master_dokumen_perijinan_kapal->update($request->all());

        return redirect()->route('master-dokumen-perijinan-kapal.index')
            ->with('success', 'Dokumen perijinan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DokumenPerijinanKapal $master_dokumen_perijinan_kapal)
    {
        $master_dokumen_perijinan_kapal->delete();

        return redirect()->route('master-dokumen-perijinan-kapal.index')
            ->with('success', 'Dokumen perijinan berhasil dihapus.');
    }
}
