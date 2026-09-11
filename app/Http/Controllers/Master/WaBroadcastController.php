<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ManifestController;
use App\Models\Manifest;
use App\Models\WaBroadcast;
use App\Models\WaTemplate;
use Illuminate\Http\Request;

class WaBroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = WaBroadcast::with('template')->orderBy('id', 'desc')->get();

        return view('master.wa-broadcast.index', compact('broadcasts'));
    }

    public function create()
    {
        $templates = WaTemplate::where('is_active', true)->orderBy('nama_template')->get();

        // Get unique ships from Manifest
        $kapals = Manifest::select('nama_kapal')
            ->distinct()
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '!=', '')
            ->orderBy('nama_kapal')
            ->pluck('nama_kapal')
            ->map(fn ($k) => trim($k))
            ->unique()
            ->filter()
            ->values();

        $selectedKapal = old('nama_kapal');
        $voyages = collect();
        if ($selectedKapal) {
            $voyages = Manifest::where('nama_kapal', $selectedKapal)
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->orderBy('no_voyage', 'desc')
                ->pluck('no_voyage')
                ->map(fn ($v) => trim($v))
                ->unique()
                ->filter()
                ->values();
        }

        return view('master.wa-broadcast.create', compact('templates', 'kapals', 'voyages'));
    }

    public function getVoyages(Request $request)
    {
        $namaKapal = $request->query('nama_kapal');

        if (! $namaKapal) {
            return response()->json([
                'success' => true,
                'voyages' => [],
            ]);
        }

        $voyages = Manifest::where('nama_kapal', $namaKapal)
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->orderBy('no_voyage', 'desc')
            ->pluck('no_voyage');

        if ($voyages->isEmpty()) {
            $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
            $normalizedKapal = preg_replace('/\s+/', ' ', $normalizedKapal);

            $voyages = Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->whereNotNull('no_voyage')
                ->where('no_voyage', '!=', '')
                ->distinct()
                ->orderBy('no_voyage', 'desc')
                ->pluck('no_voyage');
        }

        $voyages = $voyages->map(fn ($v) => trim($v))
            ->unique()
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'voyages' => $voyages,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
            'kategori_masalah' => 'required|string',
            'deskripsi_masalah' => 'nullable|string',
            'template_id' => 'required|exists:wa_templates,id',
        ]);

        // Calculate total affected shippers (similar logic to broadcastPreview)
        $manifests = Manifest::where('nama_kapal', $request->nama_kapal)
            ->where('no_voyage', $request->no_voyage)
            ->get();

        $shippers = $manifests->groupBy(function ($item) {
            return $item->shipper_id ? 'shipper_'.$item->shipper_id : 'pengirim_'.$item->pengirim;
        });

        $totalShipper = $shippers->count();

        // Save broadcast history
        WaBroadcast::create([
            'nama_kapal' => $request->nama_kapal,
            'no_voyage' => $request->no_voyage,
            'kategori_masalah' => $request->kategori_masalah,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'wa_template_id' => $request->template_id,
            'total_shipper' => $totalShipper,
        ]);

        // Forward the request to ManifestController's broadcastPreview method
        // so we don't have to duplicate the complex preview logic
        $manifestController = app(ManifestController::class);

        return $manifestController->broadcastPreview($request);
    }

    public function destroy(WaBroadcast $waBroadcast)
    {
        $waBroadcast->delete();

        return redirect()->route('master.wa-broadcast.index')->with('success', 'Riwayat broadcast berhasil dihapus.');
    }
}
