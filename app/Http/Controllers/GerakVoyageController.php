<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Manifest;
use App\Models\MasterKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerakVoyageController extends Controller
{
    public function dashboard(Request $request)
    {
        $filters = $request->validate([
            'nama_kapal' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'status' => 'nullable|in:terisi,belum_terisi,jam_belum_lengkap',
        ]);

        $tanggalFields = [
            'tanggal_muat',
            'tanggal_mulai_berlayar',
            'tanggal_berlabuh',
            'tanggal_sandar',
            'tanggal_mulai_bongkar',
            'tanggal_selesai_bongkar',
        ];

        $alexindoShips = MasterKapal::query()
            ->whereRaw('UPPER(TRIM(pelayaran)) = ?', ['PT. ALEXINDO YAKIN PRIMA'])
            ->whereNotNull('nama_kapal')
            ->pluck('nama_kapal')
            ->map(fn ($name) => strtoupper(str_replace('  ', ' ', str_replace('.', '', trim($name)))))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $grouped = Manifest::query()
            ->select('nama_kapal', 'no_voyage')
            ->selectRaw('COUNT(*) as jumlah_manifest')
            ->selectRaw('MAX(COALESCE(tanggal_berangkat, tanggal_muat, DATE(created_at))) as tanggal_voyage')
            ->selectRaw('MAX(id) as manifest_terakhir_id')
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '<>', '')
            ->whereIn(DB::raw("UPPER(REPLACE(REPLACE(TRIM(nama_kapal), '.', ''), '  ', ' '))"), $alexindoShips)
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '<>', '')
            ->groupBy('nama_kapal', 'no_voyage');

        foreach ($tanggalFields as $field) {
            $grouped->selectRaw("MAX({$field}) as {$field}");
            $jamField = str_replace('tanggal_', 'jam_', $field);
            $grouped->selectRaw("MAX({$jamField}) as {$jamField}");
        }

        $hasDate = function ($voyage) use ($tanggalFields): bool {
            foreach ($tanggalFields as $field) {
                if ($voyage->{$field}) {
                    return true;
                }
            }

            return false;
        };

        $hasMissingTime = function ($voyage) use ($tanggalFields): bool {
            foreach ($tanggalFields as $field) {
                $jamField = str_replace('tanggal_', 'jam_', $field);
                if ($voyage->{$field} && ! $voyage->{$jamField}) {
                    return true;
                }
            }

            return false;
        };

        // Pilih satu voyage paling baru untuk tiap kapal sebelum filter status diterapkan.
        $latestByShip = $grouped->toBase()->get()
            ->sort(function ($a, $b) {
                $dateOrder = strcmp((string) $b->tanggal_voyage, (string) $a->tanggal_voyage);

                return $dateOrder ?: ((int) $b->manifest_terakhir_id <=> (int) $a->manifest_terakhir_id);
            })
            ->unique('nama_kapal')
            ->values();

        $ships = $latestByShip->pluck('nama_kapal')->sort()->values();

        $filtered = $latestByShip->filter(function ($voyage) use ($filters) {
            return (empty($filters['nama_kapal']) || $voyage->nama_kapal === $filters['nama_kapal'])
                && (empty($filters['no_voyage']) || stripos($voyage->no_voyage, $filters['no_voyage']) !== false);
        });

        $totalShips = $filtered->count();
        $shipsWithDates = $filtered->filter($hasDate)->count();
        $shipsWithoutDates = $totalShips - $shipsWithDates;

        if (($filters['status'] ?? null) === 'terisi') {
            $filtered = $filtered->filter($hasDate);
        } elseif (($filters['status'] ?? null) === 'belum_terisi') {
            $filtered = $filtered->reject($hasDate);
        } elseif (($filters['status'] ?? null) === 'jam_belum_lengkap') {
            $filtered = $filtered->filter($hasMissingTime);
        }

        $filtered = $filtered->sortBy('nama_kapal')->values();
        $voyages = $filtered;

        return view('gerak-voyage.dashboard', compact(
            'voyages', 'ships', 'filters', 'tanggalFields',
            'totalShips', 'shipsWithDates', 'shipsWithoutDates'
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
        $tanggalMuatOb = $obMuat?->tanggal_muat;
        $tanggalMuatDefault = $manifest?->tanggal_muat ?: $tanggalMuatOb;

        return view('gerak-voyage.create', compact('namaKapal', 'noVoyage', 'manifest', 'tanggalMuatOb', 'tanggalMuatDefault'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
            'tanggal_mulai_berlayar' => 'nullable|date|required_with:jam_mulai_berlayar',
            'jam_mulai_berlayar' => 'nullable|date_format:H:i',
            'tanggal_berlabuh' => 'nullable|date|required_with:jam_berlabuh',
            'jam_berlabuh' => 'nullable|date_format:H:i',
            'tanggal_sandar' => 'nullable|date|required_with:jam_sandar',
            'jam_sandar' => 'nullable|date_format:H:i',
            'tanggal_mulai_bongkar' => 'nullable|date|required_with:jam_mulai_bongkar',
            'jam_mulai_bongkar' => 'nullable|date_format:H:i',
            'tanggal_selesai_bongkar' => 'nullable|date|required_with:jam_selesai_bongkar',
            'jam_selesai_bongkar' => 'nullable|date_format:H:i',
            'tanggal_muat' => 'nullable|date|required_with:jam_muat',
            'jam_muat' => 'nullable|date_format:H:i',
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
                'tanggal_mulai_berlayar' => $validated['tanggal_mulai_berlayar'] ?? null,
                'jam_mulai_berlayar' => $validated['jam_mulai_berlayar'] ?? null,
                'tanggal_berlabuh' => $validated['tanggal_berlabuh'] ?? null,
                'jam_berlabuh' => $validated['jam_berlabuh'] ?? null,
                'tanggal_sandar' => $validated['tanggal_sandar'] ?? null,
                'jam_sandar' => $validated['jam_sandar'] ?? null,
                'tanggal_mulai_bongkar' => $validated['tanggal_mulai_bongkar'] ?? null,
                'jam_mulai_bongkar' => $validated['jam_mulai_bongkar'] ?? null,
                'tanggal_selesai_bongkar' => $validated['tanggal_selesai_bongkar'] ?? null,
                'jam_selesai_bongkar' => $validated['jam_selesai_bongkar'] ?? null,
                'tanggal_muat' => $validated['tanggal_muat'],
                'jam_muat' => $validated['jam_muat'] ?? null,
            ]);

        return redirect()->route('gerak-voyage.index')->with('success', "Tanggal dan jam Gerak Voyage berhasil disimpan untuk {$updatedCount} manifest.");
    }
}
