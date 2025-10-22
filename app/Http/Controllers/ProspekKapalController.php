<?php

namespace App\Http\Controllers;

use App\Models\ProspekKapal;
use App\Models\ProspekKapalKontainer;
use App\Models\PergerakanKapal;
use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProspekKapalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:prospek-kapal-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:prospek-kapal-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:prospek-kapal-update', ['only' => ['edit', 'update', 'updateKontainerStatus']]);
        $this->middleware('permission:prospek-kapal-delete', ['only' => ['destroy', 'removeKontainer']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProspekKapal::with(['pergerakanKapal', 'kontainers']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Voyage filter
        if ($request->filled('voyage')) {
            $query->byVoyage($request->voyage);
        }

        $prospekKapals = $query->orderBy('tanggal_loading', 'desc')->paginate(15);

        return view('prospek-kapal.index', compact('prospekKapals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available voyages from pergerakan kapal that don't have prospek kapal yet
        $availableVoyages = PergerakanKapal::whereNotIn('id', function($query) {
            $query->select('pergerakan_kapal_id')
                  ->from('prospek_kapal')
                  ->whereNotNull('pergerakan_kapal_id');
        })
        ->where('status', '!=', 'cancelled')
        ->orderBy('tanggal_sandar', 'desc')
        ->get();

        return view('prospek-kapal.create', compact('availableVoyages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pergerakan_kapal_id' => 'required|exists:pergerakan_kapal,id',
            'tanggal_loading' => 'required|date',
            'estimasi_departure' => 'nullable|date|after:tanggal_loading',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $pergerakanKapal = PergerakanKapal::findOrFail($validated['pergerakan_kapal_id']);

            $prospekKapal = ProspekKapal::create([
                'pergerakan_kapal_id' => $validated['pergerakan_kapal_id'],
                'voyage' => $pergerakanKapal->voyage,
                'nama_kapal' => $pergerakanKapal->nama_kapal,
                'tanggal_loading' => $validated['tanggal_loading'],
                'estimasi_departure' => $validated['estimasi_departure'],
                'jumlah_kontainer_terjadwal' => 0,
                'jumlah_kontainer_loaded' => 0,
                'status' => 'draft',
                'keterangan' => $validated['keterangan'],
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('prospek-kapal.show', $prospekKapal)
                           ->with('success', 'Prospek kapal berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Gagal membuat prospek kapal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProspekKapal $prospekKapal)
    {
        $prospekKapal->load(['pergerakanKapal', 'kontainers.tandaTerima', 'kontainers.tandaTerimaTanpaSuratJalan']);

        // Get available containers from tanda terima that:
        // 1. Have status 'approved'
        // 2. Have matching estimasi_nama_kapal with this prospek kapal's nama_kapal
        // 3. Haven't been added to this specific prospek kapal yet
        $availableTandaTerima = TandaTerima::where('status', 'approved')
            ->where('estimasi_nama_kapal', $prospekKapal->nama_kapal)
            ->whereNotIn('id', function($query) use ($prospekKapal) {
                $query->select('tanda_terima_id')
                      ->from('prospek_kapal_kontainers')
                      ->where('prospek_kapal_id', $prospekKapal->id)
                      ->whereNotNull('tanda_terima_id');
            })
            ->get();

        // For TandaTerimaTanpaSJ - check estimasi_naik_kapal field
        $availableTandaTerimaTanpaSJ = TandaTerimaTanpaSuratJalan::where('status', 'approved')
            ->where('estimasi_naik_kapal', $prospekKapal->nama_kapal)
            ->whereNotIn('id', function($query) use ($prospekKapal) {
                $query->select('tanda_terima_tanpa_sj_id')
                      ->from('prospek_kapal_kontainers')
                      ->where('prospek_kapal_id', $prospekKapal->id)
                      ->whereNotNull('tanda_terima_tanpa_sj_id');
            })
            ->get();

        return view('prospek-kapal.show', compact('prospekKapal', 'availableTandaTerima', 'availableTandaTerimaTanpaSJ'));
    }

    /**
     * Add containers to prospek kapal
     */
    public function addKontainers(Request $request, ProspekKapal $prospekKapal)
    {
        $validated = $request->validate([
            'tanda_terima_ids' => 'array',
            'tanda_terima_ids.*' => 'exists:tanda_terimas,id',
            'tanda_terima_tanpa_sj_ids' => 'array',
            'tanda_terima_tanpa_sj_ids.*' => 'exists:tanda_terima_tanpa_surat_jalan,id',
        ]);

        try {
            DB::beginTransaction();

            $addedContainers = 0;

            // Add containers from tanda terima
            if (isset($validated['tanda_terima_ids'])) {
                foreach ($validated['tanda_terima_ids'] as $tandaTerimaId) {
                    $tandaTerima = TandaTerima::findOrFail($tandaTerimaId);

                    // Parse container numbers from no_kontainer field
                    $kontainerNumbers = $this->parseKontainerNumbers($tandaTerima->no_kontainer);

                    foreach ($kontainerNumbers as $index => $nomorKontainer) {
                        ProspekKapalKontainer::create([
                            'prospek_kapal_id' => $prospekKapal->id,
                            'tanda_terima_id' => $tandaTerimaId,
                            'nomor_kontainer' => $nomorKontainer,
                            'ukuran_kontainer' => $tandaTerima->size . 'ft',
                            'no_seal' => $tandaTerima->no_seal,
                            'loading_sequence' => $addedContainers + $index + 1,
                            'status_loading' => 'pending',
                            'created_by' => Auth::id(),
                        ]);
                    }

                    $addedContainers += count($kontainerNumbers);
                }
            }

            // Add containers from tanda terima tanpa surat jalan
            if (isset($validated['tanda_terima_tanpa_sj_ids'])) {
                foreach ($validated['tanda_terima_tanpa_sj_ids'] as $tandaTerimaTanpaSjId) {
                    $tandaTerimaTanpaSj = TandaTerimaTanpaSuratJalan::findOrFail($tandaTerimaTanpaSjId);

                    // Parse container numbers from no_kontainer field
                    $kontainerNumbers = $this->parseKontainerNumbers($tandaTerimaTanpaSj->no_kontainer);

                    foreach ($kontainerNumbers as $index => $nomorKontainer) {
                        ProspekKapalKontainer::create([
                            'prospek_kapal_id' => $prospekKapal->id,
                            'tanda_terima_tanpa_sj_id' => $tandaTerimaTanpaSjId,
                            'nomor_kontainer' => $nomorKontainer,
                            'ukuran_kontainer' => $tandaTerimaTanpaSj->size . 'ft',
                            'no_seal' => $tandaTerimaTanpaSj->no_seal,
                            'loading_sequence' => $addedContainers + $index + 1,
                            'status_loading' => 'pending',
                            'created_by' => Auth::id(),
                        ]);
                    }

                    $addedContainers += count($kontainerNumbers);
                }
            }

            // Update jumlah kontainer terjadwal
            $prospekKapal->update([
                'jumlah_kontainer_terjadwal' => $prospekKapal->kontainers()->count(),
                'status' => 'scheduled',
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', "Berhasil menambahkan {$addedContainers} kontainer ke prospek kapal.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menambahkan kontainer: ' . $e->getMessage());
        }
    }

    /**
     * Update container status in loading process
     */
    public function updateKontainerStatus(Request $request, ProspekKapalKontainer $kontainer)
    {
        $validated = $request->validate([
            'status_loading' => 'required|in:pending,ready,loading,loaded,problem',
            'tanggal_loading' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $kontainer->update([
                'status_loading' => $validated['status_loading'],
                'tanggal_loading' => $validated['tanggal_loading'],
                'keterangan' => $validated['keterangan'],
                'updated_by' => Auth::id(),
            ]);

            // Update prospek kapal loaded count
            $prospekKapal = $kontainer->prospekKapal;
            $loadedCount = $prospekKapal->kontainers()->where('status_loading', 'loaded')->count();

            $status = 'scheduled';
            if ($loadedCount > 0 && $loadedCount < $prospekKapal->jumlah_kontainer_terjadwal) {
                $status = 'loading';
            } elseif ($loadedCount >= $prospekKapal->jumlah_kontainer_terjadwal) {
                $status = 'completed';
            }

            $prospekKapal->update([
                'jumlah_kontainer_loaded' => $loadedCount,
                'status' => $status,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'Status kontainer berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengupdate status kontainer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProspekKapal $prospekKapal)
    {
        try {
            DB::beginTransaction();

            // Delete all related containers first
            $prospekKapal->kontainers()->delete();

            // Delete the prospek kapal
            $prospekKapal->delete();

            DB::commit();

            return redirect()->route('prospek-kapal.index')
                           ->with('success', 'Prospek kapal berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus prospek kapal: ' . $e->getMessage());
        }
    }

    /**
     * Parse container numbers from string (handles comma-separated or array format)
     */
    private function parseKontainerNumbers($noKontainer)
    {
        if (is_array($noKontainer)) {
            return array_filter($noKontainer);
        }

        if (is_string($noKontainer)) {
            // Handle JSON format or comma-separated
            $decoded = json_decode($noKontainer, true);
            if (is_array($decoded)) {
                return array_filter($decoded);
            }

            // Handle comma-separated format
            return array_filter(array_map('trim', explode(',', $noKontainer)));
        }

        return [$noKontainer];
    }
}
