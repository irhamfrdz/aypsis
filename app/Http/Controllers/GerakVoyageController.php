<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GerakVoyageController extends Controller
{
    public function index()
    {
        // Get list of ships from manifests table
        $shipsFromManifests = \App\Models\Manifest::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Get ships from naik_kapal table as well
        $shipsFromNaikKapal = \App\Models\NaikKapal::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Merge and get unique ship names
        $shipNames = $shipsFromManifests->merge($shipsFromNaikKapal)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Convert to objects for view compatibility
        $ships = $shipNames->map(function ($name) {
            return (object) ['nama_kapal' => $name];
        });

        return view('gerak-voyage.index', compact('ships'));
    }

    public function create(Request $request)
    {
        $namaKapal = $request->input('nama_kapal');
        $noVoyage = $request->input('no_voyage');

        if (!$namaKapal || !$noVoyage) {
            return redirect()->route('gerak-voyage.index')->with('error', 'Silakan pilih kapal dan voyage terlebih dahulu.');
        }

        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $cleanNoVoyage = trim($noVoyage);

        // Get first manifest to populate current dates if they exist
        $manifest = \App\Models\Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $cleanNoVoyage)
            ->first();

        return view('gerak-voyage.create', compact('namaKapal', 'noVoyage', 'manifest'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
            'tanggal_mulai_berlayar' => 'nullable|date',
            'tanggal_berlabuh' => 'nullable|date',
            'tanggal_sandar' => 'nullable|date',
            'tanggal_mulai_bongkar' => 'nullable|date',
            'tanggal_selesai_bongkar' => 'nullable|date',
        ]);

        $namaKapal = $validated['nama_kapal'];
        $noVoyage = $validated['no_voyage'];

        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $cleanNoVoyage = trim($noVoyage);

        // Update all manifests with this ship and voyage
        $updatedCount = \App\Models\Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $cleanNoVoyage)
            ->update([
                'tanggal_mulai_berlayar' => $validated['tanggal_mulai_berlayar'],
                'tanggal_berlabuh' => $validated['tanggal_berlabuh'],
                'tanggal_sandar' => $validated['tanggal_sandar'],
                'tanggal_mulai_bongkar' => $validated['tanggal_mulai_bongkar'],
                'tanggal_selesai_bongkar' => $validated['tanggal_selesai_bongkar'],
            ]);

        return redirect()->route('gerak-voyage.index')->with('success', "Data Gerak Voyage berhasil disimpan untuk {$updatedCount} manifest.");
    }
}
