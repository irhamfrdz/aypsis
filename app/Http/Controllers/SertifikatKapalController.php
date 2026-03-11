<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SertifikatKapal;

class SertifikatKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SertifikatKapal::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_sertifikat', 'like', '%' . $search . '%')
                  ->orWhere('name_certificate', 'like', '%' . $search . '%')
                  ->orWhere('nickname', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sertifikats = $query->latest()->paginate($request->get('per_page', 10));

        return view('master-sertifikat-kapal.index', compact('sertifikats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-sertifikat-kapal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_sertifikat' => 'required|string|max:255',
            'name_certificate' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        SertifikatKapal::create($request->all());

        return redirect()->route('master-sertifikat-kapal.index')
            ->with('success', 'Sertifikat kapal berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SertifikatKapal $master_sertifikat_kapal)
    {
        return view('master-sertifikat-kapal.show', compact('master_sertifikat_kapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SertifikatKapal $master_sertifikat_kapal)
    {
        return view('master-sertifikat-kapal.edit', compact('master_sertifikat_kapal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SertifikatKapal $master_sertifikat_kapal)
    {
        $request->validate([
            'nama_sertifikat' => 'required|string|max:255',
            'name_certificate' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $master_sertifikat_kapal->update($request->all());

        return redirect()->route('master-sertifikat-kapal.index')
            ->with('success', 'Sertifikat kapal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SertifikatKapal $master_sertifikat_kapal)
    {
        $master_sertifikat_kapal->delete();

        return redirect()->route('master-sertifikat-kapal.index')
            ->with('success', 'Sertifikat kapal berhasil dihapus.');
    }
}
