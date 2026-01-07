<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Manifest;
use App\Models\Prospek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManifestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manifest-view')->only(['index', 'show']);
        $this->middleware('permission:manifest-create')->only(['create', 'store']);
        $this->middleware('permission:manifest-edit')->only(['edit', 'update']);
        $this->middleware('permission:manifest-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if kapal and voyage filters are provided
        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');

        // If no filters, redirect to select ship page
        if (!$namaKapal || !$noVoyage) {
            return redirect()->route('report.manifests.select-ship');
        }

        // Show filtered manifest data
        return $this->showManifestData($request, $namaKapal, $noVoyage);
    }

    /**
     * Display ship selection page
     */
    public function selectShip(Request $request)
    {
        // Get list of ships from manifests table
        $shipsFromManifests = Manifest::whereNotNull('nama_kapal')
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
            return (object)['nama_kapal' => $name];
        });

        return view('manifests.select-ship', compact('ships'));
    }

    /**
     * Display manifest data for selected ship and voyage
     */
    private function showManifestData(Request $request, $namaKapal, $noVoyage)
    {
        $query = Manifest::with(['prospek', 'createdBy', 'updatedBy'])
            ->where('nama_kapal', $namaKapal)
            ->where('no_voyage', $noVoyage);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_bl', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'LIKE', "%{$search}%")
                  ->orWhere('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('pengirim', 'LIKE', "%{$search}%")
                  ->orWhere('penerima', 'LIKE', "%{$search}%");
            });
        }

        // Filter by tipe kontainer
        if ($request->filled('tipe_kontainer')) {
            $query->where('tipe_kontainer', $request->tipe_kontainer);
        }

        // Filter by size kontainer
        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

        $manifests = $query->orderBy('created_at', 'desc')->paginate(20);

        // Store selection in session
        session([
            'selected_manifest_ship' => $namaKapal,
            'selected_manifest_voyage' => $noVoyage
        ]);

        return view('manifests.index', compact('manifests', 'namaKapal', 'noVoyage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prospeks = Prospek::orderBy('nama_perusahaan')->get();
        return view('manifests.create', compact('prospeks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_bl' => 'required|string|max:255',
            'prospek_id' => 'nullable|exists:prospek,id',
            'nomor_kontainer' => 'required|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'pelabuhan_asal' => 'nullable|string|max:255',
            'pelabuhan_tujuan' => 'nullable|string|max:255',
            'nama_kapal' => 'nullable|string|max:255',
            'tanggal_berangkat' => 'nullable|date',
            'nama_barang' => 'nullable|string',
            'asal_kontainer' => 'nullable|string|max:255',
            'ke' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'alamat_pengiriman' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'tonnage' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'kuantitas' => 'nullable|integer',
            'penerimaan' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Manifest::create($validated);

        return redirect()->route('report.manifests.index')->with('success', 'Manifest berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $manifest = Manifest::with(['prospek', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('manifests.show', compact('manifest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $manifest = Manifest::findOrFail($id);
        $prospeks = Prospek::orderBy('nama_perusahaan')->get();
        return view('manifests.edit', compact('manifest', 'prospeks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $manifest = Manifest::findOrFail($id);

        $validated = $request->validate([
            'nomor_bl' => 'required|string|max:255',
            'prospek_id' => 'nullable|exists:prospek,id',
            'nomor_kontainer' => 'required|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'pelabuhan_asal' => 'nullable|string|max:255',
            'pelabuhan_tujuan' => 'nullable|string|max:255',
            'nama_kapal' => 'nullable|string|max:255',
            'tanggal_berangkat' => 'nullable|date',
            'nama_barang' => 'nullable|string',
            'asal_kontainer' => 'nullable|string|max:255',
            'ke' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'alamat_pengiriman' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'tonnage' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'kuantitas' => 'nullable|integer',
            'penerimaan' => 'nullable|date',
        ]);

        $validated['updated_by'] = Auth::id();

        $manifest->update($validated);

        return redirect()->route('report.manifests.index')->with('success', 'Manifest berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $manifest = Manifest::findOrFail($id);
        $manifest->delete();

        return redirect()->route('report.manifests.index')->with('success', 'Manifest berhasil dihapus');
    }

    /**
     * Update nomor BL via AJAX
     */
    public function updateNomorBl(Request $request, string $id)
    {
        $manifest = Manifest::findOrFail($id);

        $validated = $request->validate([
            'nomor_bl' => 'required|string|max:255',
        ]);

        $validated['updated_by'] = Auth::id();

        $manifest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nomor BL berhasil diperbarui',
            'nomor_bl' => $manifest->nomor_bl
        ]);
    }

    /**
     * Import manifests from Excel/CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
        ]);

        try {
            $file = $request->file('file');
            $namaKapal = $request->input('nama_kapal');
            $noVoyage = $request->input('no_voyage');

            $import = new \App\Imports\ManifestImport($namaKapal, $noVoyage);
            $result = $import->import($file);

            if ($result === false) {
                return redirect()->back()
                    ->with('error', 'Import gagal: ' . implode(', ', $import->getErrors()));
            }

            $successCount = $result['success_count'];
            $errors = $result['errors'];

            if ($successCount > 0 && empty($errors)) {
                return redirect()->route('report.manifests.index', [
                    'nama_kapal' => $namaKapal,
                    'no_voyage' => $noVoyage
                ])->with('success', "Berhasil import {$successCount} data manifest");
            } elseif ($successCount > 0 && !empty($errors)) {
                return redirect()->route('report.manifests.index', [
                    'nama_kapal' => $namaKapal,
                    'no_voyage' => $noVoyage
                ])->with('warning', "Import selesai dengan {$successCount} data berhasil, namun ada " . count($errors) . " error: " . implode('; ', array_slice($errors, 0, 3)));
            } else {
                return redirect()->back()
                    ->with('error', 'Import gagal: ' . implode('; ', array_slice($errors, 0, 5)));
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import
     */
    public function downloadTemplate()
    {
        $headers = [
            'No BL',
            'No Manifest',
            'No Kontainer',
            'No Seal',
            'Tipe Kontainer',
            'Size Kontainer',
            'Nama Barang',
            'Pengirim',
            'Penerima',
            'Term'
        ];

        $filename = 'template_import_manifest.csv';
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($file, $headers, ';');
            
            // Write example data
            fputcsv($file, [
                'BL001',
                'MN001',
                'CONT001',
                'SEAL001',
                'Dry Container',
                '20',
                'Barang Contoh',
                'PT Pengirim',
                'PT Penerima',
                'FOB'
            ], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}

