<?php

namespace App\Http\Controllers;

use App\Models\SertifikatKapal;
use Illuminate\Http\Request;

class MasterSertifikatKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = SertifikatKapal::latest()->paginate(15);
        return view('master-sertifikat-kapal.index', compact('items'));
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
            'nama_sertifikat' => 'required|string|max:255|unique:sertifikat_kapals,nama_sertifikat',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        SertifikatKapal::create($request->all());

        return redirect()->route('master-sertifikat-kapal.index')
                         ->with('success', 'Sertifikat Kapal berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = SertifikatKapal::findOrFail($id);
        return view('master-sertifikat-kapal.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = SertifikatKapal::findOrFail($id);

        $request->validate([
            'nama_sertifikat' => 'required|string|max:255|unique:sertifikat_kapals,nama_sertifikat,' . $id,
            'keterangan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $item->update($request->all());

        return redirect()->route('master-sertifikat-kapal.index')
                         ->with('success', 'Sertifikat Kapal berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = SertifikatKapal::findOrFail($id);
        $item->delete();

        return redirect()->route('master-sertifikat-kapal.index')
                         ->with('success', 'Sertifikat Kapal berhasil dihapus');
    }
}
