<?php

namespace App\Http\Controllers;

use App\Exports\MasterJadwalKapalBerlabuhExport;
use App\Models\MasterJadwalKapalBerlabuh;
use App\Models\MasterKapal;
use App\Models\MasterPelabuhan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MasterJadwalKapalBerlabuhController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master-jadwal-kapal-berlabuh-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:master-jadwal-kapal-berlabuh-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:master-jadwal-kapal-berlabuh-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:master-jadwal-kapal-berlabuh-delete', ['only' => ['destroy']]);
        $this->middleware('permission:master-jadwal-kapal-berlabuh-export', ['only' => ['export']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterJadwalKapalBerlabuh::query()->with(['kapal', 'pelabuhanRelation']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter Pelabuhan
        if ($request->filled('pelabuhan')) {
            $query->byPelabuhan($request->pelabuhan);
        }

        // Filter Kapal
        if ($request->filled('nama_kapal')) {
            $query->byKapal($request->nama_kapal);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Date range filter based on ETD / ETA
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_etd', [$request->start_date, $request->end_date]);
        }

        // Sorting
        $query->orderBy('pelabuhan', 'asc')->orderBy('tanggal_etd', 'asc');

        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [10, 25, 50, 100, 200]) ? $perPage : 50;

        $jadwals = $query->paginate($perPage)->withQueryString();

        // Statistics
        $stats = [
            'total' => MasterJadwalKapalBerlabuh::count(),
            'aktif' => MasterJadwalKapalBerlabuh::where('status', 'aktif')->count(),
            'selesai' => MasterJadwalKapalBerlabuh::where('status', 'selesai')->count(),
            'batal' => MasterJadwalKapalBerlabuh::where('status', 'batal')->count(),
        ];

        // Dropdown data for filter
        $pelabuhanList = MasterJadwalKapalBerlabuh::whereNotNull('pelabuhan')
            ->distinct()
            ->orderBy('pelabuhan')
            ->pluck('pelabuhan');

        $kapalList = MasterJadwalKapalBerlabuh::whereNotNull('nama_kapal')
            ->distinct()
            ->orderBy('nama_kapal')
            ->pluck('nama_kapal');

        return view('master-jadwal-kapal-berlabuh.index', compact('jadwals', 'stats', 'pelabuhanList', 'kapalList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $masterKapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal', 'asc')
            ->get();

        $masterPelabuhans = MasterPelabuhan::where('status', 'aktif')
            ->orderBy('nama_pelabuhan', 'asc')
            ->get();

        return view('master-jadwal-kapal-berlabuh.create', compact('masterKapals', 'masterPelabuhans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelabuhan' => 'required|string|max:255',
            'master_pelabuhan_id' => 'nullable|exists:master_pelabuhans,id',
            'nama_kapal' => 'required|string|max:255',
            'master_kapal_id' => 'nullable|exists:master_kapals,id',
            'no_voyage' => 'nullable|string|max:100',
            'tanggal_closing' => 'nullable|date',
            'tanggal_etd' => 'nullable|date',
            'tanggal_eta' => 'nullable|date',
            'status' => 'required|in:aktif,selesai,batal',
            'keterangan' => 'nullable|string',
        ], [
            'pelabuhan.required' => 'Pelabuhan / Rute Tujuan wajib diisi.',
            'nama_kapal.required' => 'Nama kapal wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Auto-match master_kapal_id if not explicitly provided
        if (empty($validated['master_kapal_id'])) {
            $kapal = MasterKapal::where('nama_kapal', $validated['nama_kapal'])->first();
            if ($kapal) {
                $validated['master_kapal_id'] = $kapal->id;
            }
        }

        // Auto-match master_pelabuhan_id if not explicitly provided
        if (empty($validated['master_pelabuhan_id'])) {
            $pelabuhan = MasterPelabuhan::where('nama_pelabuhan', $validated['pelabuhan'])->first();
            if ($pelabuhan) {
                $validated['master_pelabuhan_id'] = $pelabuhan->id;
            }
        }

        MasterJadwalKapalBerlabuh::create($validated);

        return redirect()->route('master-jadwal-kapal-berlabuh.index')
            ->with('success', 'Jadwal kapal berlabuh berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jadwal = MasterJadwalKapalBerlabuh::with(['kapal', 'pelabuhanRelation', 'creator', 'updater'])
            ->findOrFail($id);

        return view('master-jadwal-kapal-berlabuh.show', compact('jadwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $jadwal = MasterJadwalKapalBerlabuh::findOrFail($id);

        $masterKapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal', 'asc')
            ->get();

        $masterPelabuhans = MasterPelabuhan::where('status', 'aktif')
            ->orderBy('nama_pelabuhan', 'asc')
            ->get();

        return view('master-jadwal-kapal-berlabuh.edit', compact('jadwal', 'masterKapals', 'masterPelabuhans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jadwal = MasterJadwalKapalBerlabuh::findOrFail($id);

        $validated = $request->validate([
            'pelabuhan' => 'required|string|max:255',
            'master_pelabuhan_id' => 'nullable|exists:master_pelabuhans,id',
            'nama_kapal' => 'required|string|max:255',
            'master_kapal_id' => 'nullable|exists:master_kapals,id',
            'no_voyage' => 'nullable|string|max:100',
            'tanggal_closing' => 'nullable|date',
            'tanggal_etd' => 'nullable|date',
            'tanggal_eta' => 'nullable|date',
            'status' => 'required|in:aktif,selesai,batal',
            'keterangan' => 'nullable|string',
        ], [
            'pelabuhan.required' => 'Pelabuhan / Rute Tujuan wajib diisi.',
            'nama_kapal.required' => 'Nama kapal wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        if (empty($validated['master_kapal_id'])) {
            $kapal = MasterKapal::where('nama_kapal', $validated['nama_kapal'])->first();
            $validated['master_kapal_id'] = $kapal ? $kapal->id : null;
        }

        if (empty($validated['master_pelabuhan_id'])) {
            $pelabuhan = MasterPelabuhan::where('nama_pelabuhan', $validated['pelabuhan'])->first();
            $validated['master_pelabuhan_id'] = $pelabuhan ? $pelabuhan->id : null;
        }

        $jadwal->update($validated);

        return redirect()->route('master-jadwal-kapal-berlabuh.index')
            ->with('success', 'Jadwal kapal berlabuh berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jadwal = MasterJadwalKapalBerlabuh::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('master-jadwal-kapal-berlabuh.index')
            ->with('success', 'Jadwal kapal berlabuh berhasil dihapus.');
    }

    /**
     * Export data to Excel.
     */
    public function export(Request $request)
    {
        $query = MasterJadwalKapalBerlabuh::query()->with(['kapal', 'pelabuhanRelation']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('pelabuhan')) {
            $query->byPelabuhan($request->pelabuhan);
        }

        if ($request->filled('nama_kapal')) {
            $query->byKapal($request->nama_kapal);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_etd', [$request->start_date, $request->end_date]);
        }

        $sortBy = $request->get('sort_by', 'tanggal_etd');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $jadwals = $query->get();

        $filename = 'jadwal_kapal_berlabuh_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new MasterJadwalKapalBerlabuhExport($jadwals, $request->pelabuhan), $filename);
    }
}
