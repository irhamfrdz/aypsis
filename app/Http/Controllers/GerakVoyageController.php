<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Manifest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerakVoyageController extends Controller
{
    public function dashboard(Request $request)
    {
        $filters = $request->validate([
            'nama_kapal' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'status' => 'nullable|in:terisi,belum_terisi',
        ]);

        $tanggalFields = [
            'tanggal_muat',
            'tanggal_mulai_berlayar',
            'tanggal_berlabuh',
            'tanggal_sandar',
            'tanggal_mulai_bongkar',
            'tanggal_selesai_bongkar',
        ];

        $grouped = Manifest::query()
            ->select('nama_kapal', 'no_voyage')
            ->selectRaw('COUNT(*) as jumlah_manifest')
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '<>', '')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '<>', '')
            ->groupBy('nama_kapal', 'no_voyage');

        foreach ($tanggalFields as $field) {
            $grouped->selectRaw("MAX({$field}) as {$field}");
        }

        $query = DB::query()->fromSub($grouped->toBase(), 'voyages');

        if (! empty($filters['nama_kapal'])) {
            $query->where('nama_kapal', $filters['nama_kapal']);
        }

        if (! empty($filters['no_voyage'])) {
            $query->where('no_voyage', 'like', '%'.$filters['no_voyage'].'%');
        }

        $withDates = function ($query) use ($tanggalFields) {
            $query->where(function ($query) use ($tanggalFields) {
                foreach ($tanggalFields as $field) {
                    $query->orWhereNotNull($field);
                }
            });
        };

        $totalVoyages = (clone $query)->count();
        $filledQuery = clone $query;
        $withDates($filledQuery);
        $voyagesWithDates = $filledQuery->count();
        $voyagesWithoutDates = $totalVoyages - $voyagesWithDates;

        if (($filters['status'] ?? null) === 'terisi') {
            $withDates($query);
        } elseif (($filters['status'] ?? null) === 'belum_terisi') {
            $query->where(function ($query) use ($tanggalFields) {
                foreach ($tanggalFields as $field) {
                    $query->whereNull($field);
                }
            });
        }

        $voyages = $query
            ->orderByRaw('COALESCE(tanggal_muat, tanggal_mulai_berlayar, tanggal_berlabuh, tanggal_sandar, tanggal_mulai_bongkar, tanggal_selesai_bongkar) DESC')
            ->orderBy('nama_kapal')
            ->orderBy('no_voyage')
            ->paginate(15)
            ->withQueryString();

        $ships = Manifest::query()
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '<>', '')
            ->distinct()
            ->orderBy('nama_kapal')
            ->pluck('nama_kapal');

        return view('gerak-voyage.dashboard', compact(
            'voyages', 'ships', 'filters', 'tanggalFields',
            'totalVoyages', 'voyagesWithDates', 'voyagesWithoutDates'
        ));
    }

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

        // Tanggal muat utama berasal dari data OB Muat (naik_kapal), bukan
        // dari tanggal pembuatan manifest. Ambil tanggal terbaru pada voyage.
        $obMuat = \App\Models\NaikKapal::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $cleanNoVoyage)
            ->whereNotNull('tanggal_muat')
            ->orderByDesc('tanggal_muat')
            ->first();
        $tanggalMuatOb = $obMuat?->tanggal_muat ?: $manifest?->tanggal_muat;

        return view('gerak-voyage.create', compact('namaKapal', 'noVoyage', 'manifest', 'tanggalMuatOb'));
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
            'tanggal_muat' => 'nullable|date',
        ]);

        $namaKapal = $validated['nama_kapal'];
        $noVoyage = $validated['no_voyage'];

        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $cleanNoVoyage = trim($noVoyage);

        // Fallback backend jika field tanggal tidak terkirim dari form.
        if (empty($validated['tanggal_muat'])) {
            $tanggalMuatOb = \App\Models\NaikKapal::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->where('no_voyage', $cleanNoVoyage)
                ->whereNotNull('tanggal_muat')
                ->orderByDesc('tanggal_muat')
                ->value('tanggal_muat');
            $validated['tanggal_muat'] = $tanggalMuatOb;
        }

        // Update all manifests with this ship and voyage
        $updatedCount = \App\Models\Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $cleanNoVoyage)
            ->update([
                'tanggal_mulai_berlayar' => $validated['tanggal_mulai_berlayar'],
                'tanggal_berlabuh' => $validated['tanggal_berlabuh'],
                'tanggal_sandar' => $validated['tanggal_sandar'],
                'tanggal_mulai_bongkar' => $validated['tanggal_mulai_bongkar'],
                'tanggal_selesai_bongkar' => $validated['tanggal_selesai_bongkar'],
                'tanggal_muat' => $validated['tanggal_muat'],
            ]);

        return redirect()->route('gerak-voyage.index')->with('success', "Data Gerak Voyage berhasil disimpan untuk {$updatedCount} manifest.");
    }
}
