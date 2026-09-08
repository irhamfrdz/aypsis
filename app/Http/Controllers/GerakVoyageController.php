<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GerakVoyageController extends Controller
{
    public function index()
    {
        $gerakVoyages = \App\Models\GerakVoyage::orderBy('id', 'desc')->get();
        return view('gerak-voyage.index', compact('gerakVoyages'));
    }

    public function create()
    {
        $manifests = \App\Models\Manifest::select('nama_kapal', 'no_voyage')
            ->whereNotNull('nama_kapal')
            ->whereNotNull('no_voyage')
            ->distinct()
            ->orderBy('nama_kapal')
            ->orderBy('no_voyage')
            ->get();
        
        return view('gerak-voyage.create', compact('manifests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kapal_voyage' => 'required|string',
            'tanggal_mulai_berlayar' => 'nullable|date',
            'tanggal_berlabuh' => 'nullable|date',
            'tanggal_sandar' => 'nullable|date',
            'tanggal_mulai_bongkar' => 'nullable|date',
            'tanggal_selesai_bongkar' => 'nullable|date',
        ]);

        [$nama_kapal, $no_voyage] = explode('|', $validated['kapal_voyage']);

        \App\Models\GerakVoyage::create([
            'nama_kapal' => $nama_kapal,
            'no_voyage' => $no_voyage,
            'tanggal_mulai_berlayar' => $validated['tanggal_mulai_berlayar'],
            'tanggal_berlabuh' => $validated['tanggal_berlabuh'],
            'tanggal_sandar' => $validated['tanggal_sandar'],
            'tanggal_mulai_bongkar' => $validated['tanggal_mulai_bongkar'],
            'tanggal_selesai_bongkar' => $validated['tanggal_selesai_bongkar'],
        ]);

        return redirect()->route('gerak-voyage.index')->with('success', 'Data Gerak Voyage berhasil disimpan.');
    }

    public function edit(string $id)
    {
        $gerakVoyage = \App\Models\GerakVoyage::findOrFail($id);
        $manifests = \App\Models\Manifest::select('nama_kapal', 'no_voyage')
            ->whereNotNull('nama_kapal')
            ->whereNotNull('no_voyage')
            ->distinct()
            ->orderBy('nama_kapal')
            ->orderBy('no_voyage')
            ->get();

        return view('gerak-voyage.edit', compact('gerakVoyage', 'manifests'));
    }

    public function update(Request $request, string $id)
    {
        $gerakVoyage = \App\Models\GerakVoyage::findOrFail($id);
        
        $validated = $request->validate([
            'kapal_voyage' => 'required|string',
            'tanggal_mulai_berlayar' => 'nullable|date',
            'tanggal_berlabuh' => 'nullable|date',
            'tanggal_sandar' => 'nullable|date',
            'tanggal_mulai_bongkar' => 'nullable|date',
            'tanggal_selesai_bongkar' => 'nullable|date',
        ]);

        [$nama_kapal, $no_voyage] = explode('|', $validated['kapal_voyage']);

        $gerakVoyage->update([
            'nama_kapal' => $nama_kapal,
            'no_voyage' => $no_voyage,
            'tanggal_mulai_berlayar' => $validated['tanggal_mulai_berlayar'],
            'tanggal_berlabuh' => $validated['tanggal_berlabuh'],
            'tanggal_sandar' => $validated['tanggal_sandar'],
            'tanggal_mulai_bongkar' => $validated['tanggal_mulai_bongkar'],
            'tanggal_selesai_bongkar' => $validated['tanggal_selesai_bongkar'],
        ]);

        return redirect()->route('gerak-voyage.index')->with('success', 'Data Gerak Voyage berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $gerakVoyage = \App\Models\GerakVoyage::findOrFail($id);
        $gerakVoyage->delete();

        return redirect()->route('gerak-voyage.index')->with('success', 'Data Gerak Voyage berhasil dihapus.');
    }
}
