<?php

namespace App\Http\Controllers;

use App\Models\PergerakanKapal;
use App\Models\MasterKapal;
use App\Models\MasterTujuanKirim;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PergerakanKapalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pergerakan-kapal-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:pergerakan-kapal-create', ['only' => ['create', 'store', 'generateVoyageNumber']]);
        $this->middleware('permission:pergerakan-kapal-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pergerakan-kapal-delete', ['only' => ['destroy']]);
        $this->middleware('permission:pergerakan-kapal-approve', ['only' => ['approve']]);
        $this->middleware('permission:pergerakan-kapal-print', ['only' => ['print']]);
        $this->middleware('permission:pergerakan-kapal-export', ['only' => ['export']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PergerakanKapal::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Kapal filter
        if ($request->filled('kapal')) {
            $query->byKapal($request->kapal);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $pergerakanKapals = $query->orderBy('tanggal_sandar', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => PergerakanKapal::count(),
            'scheduled' => PergerakanKapal::byStatus('scheduled')->count(),
            'sailing' => PergerakanKapal::byStatus('sailing')->count(),
            'arrived' => PergerakanKapal::byStatus('arrived')->count(),
            'departed' => PergerakanKapal::byStatus('departed')->count(),
            'delayed' => PergerakanKapal::byStatus('delayed')->count(),
            'cancelled' => PergerakanKapal::byStatus('cancelled')->count(),
        ];

        return view('pergerakan-kapal.index', compact('pergerakanKapals', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $masterKapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal', 'asc')
            ->get();

        $nahkodas = Karyawan::where('pekerjaan', 'like', '%nahkoda%')
            ->orWhere('pekerjaan', 'like', '%kapten%')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $tujuanKirims = MasterTujuanKirim::where('status', 'active')
            ->orderBy('nama_tujuan', 'asc')
            ->get();

        return view('pergerakan-kapal.create', compact('masterKapals', 'nahkodas', 'tujuanKirims'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kapal' => 'required|string|max:255',
            'kapten' => 'nullable|string|max:255',
            'voyage' => 'nullable|string|max:255',
            'transit' => 'boolean',
            'tujuan_asal' => 'required|string|max:255',
            'tujuan_tujuan' => 'required|string|max:255',
            'tujuan_transit' => 'nullable|string|max:255',
            'voyage_transit' => 'nullable|string|max:255',
            'tanggal_sandar' => 'nullable|date',
            'tanggal_labuh' => 'nullable|date',
            'tanggal_berangkat' => 'nullable|date',
            'status' => 'required|in:scheduled,sailing,arrived,departed,delayed,cancelled',
            'keterangan' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::user()->name;
        $validated['transit'] = $request->has('transit');

        PergerakanKapal::create($validated);

        return redirect()->route('pergerakan-kapal.index')
                        ->with('success', 'Data pergerakan kapal berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PergerakanKapal $pergerakanKapal)
    {
        return view('pergerakan-kapal.show', compact('pergerakanKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PergerakanKapal $pergerakanKapal)
    {
        $masterKapals = MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal', 'asc')
            ->get();

        $nahkodas = Karyawan::where('pekerjaan', 'like', '%nahkoda%')
            ->orWhere('pekerjaan', 'like', '%kapten%')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $tujuanKirims = MasterTujuanKirim::where('status', 'aktif')
            ->orderBy('nama_tujuan', 'asc')
            ->get();

        return view('pergerakan-kapal.edit', compact('pergerakanKapal', 'masterKapals', 'nahkodas', 'tujuanKirims'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PergerakanKapal $pergerakanKapal)
    {
        $validated = $request->validate([
            'nama_kapal' => 'required|string|max:255',
            'kapten' => 'nullable|string|max:255',
            'voyage' => 'nullable|string|max:255',
            'transit' => 'boolean',
            'pelabuhan_asal' => 'required|string|max:255',
            'pelabuhan_tujuan' => 'required|string|max:255',
            'pelabuhan_transit' => 'nullable|string|max:255',
            'voyage_transit' => 'nullable|string|max:255',
            'tanggal_sandar' => 'nullable|date',
            'tanggal_labuh' => 'nullable|date',
            'tanggal_berangkat' => 'nullable|date',
            'status' => 'required|in:scheduled,sailing,arrived,departed,delayed,cancelled',
            'keterangan' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::user()->name;
        $validated['transit'] = $request->has('transit');

        $pergerakanKapal->update($validated);

        return redirect()->route('pergerakan-kapal.index')
                        ->with('success', 'Data pergerakan kapal berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PergerakanKapal $pergerakanKapal)
    {
        $pergerakanKapal->delete();

        return redirect()->route('pergerakan-kapal.index')
                        ->with('success', 'Data pergerakan kapal berhasil dihapus.');
    }

    /**
     * Generate voyage number based on ship and route
     */
    public function generateVoyageNumber(Request $request)
    {
        $namaKapal = $request->nama_kapal;
        $tujuanAsal = $request->tujuan_asal;
        $tujuanTujuan = $request->tujuan_tujuan;

        if (!$namaKapal || !$tujuanAsal || !$tujuanTujuan) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Ambil nickname dari master kapal (2 digit)
        $masterKapal = MasterKapal::where('nama_kapal', $namaKapal)->first();
        if (!$masterKapal || !$masterKapal->nickname) {
            return response()->json(['error' => 'Nickname kapal tidak ditemukan'], 400);
        }
        $nicknameKapal = strtoupper(substr($masterKapal->nickname, 0, 2));

        // 2 digit nomor urut - hitung berdasarkan voyage yang sudah ada untuk kapal ini di tahun ini
        $currentYear = date('Y');
        $lastVoyageCount = PergerakanKapal::where('nama_kapal', $namaKapal)
            ->whereYear('created_at', $currentYear)
            ->count();
        $noUrut = str_pad($lastVoyageCount + 1, 2, '0', STR_PAD_LEFT);

        // Mapping kota ke kode 1 digit berdasarkan huruf pertama kota
        $kotaCodes = [
            'Jakarta' => 'J',
            'Surabaya' => 'S',
            'Medan' => 'M',
            'Makassar' => 'K',
            'Bitung' => 'T',
            'Balikpapan' => 'L',
            'Pontianak' => 'P',
            'Banjarmasin' => 'N',
            'Batam' => 'B',
            'Semarang' => 'G',
            'Palembang' => 'A',
            'Denpasar' => 'D',
            'Jayapura' => 'Y',
            'Sorong' => 'O',
            'Ambon' => 'A'
        ];

        // Ambil kota tujuan asal dan tujuan kirim
        $tujuanAsalData = MasterTujuanKirim::where('nama_tujuan', $tujuanAsal)->first();
        $tujuanTujuanData = MasterTujuanKirim::where('nama_tujuan', $tujuanTujuan)->first();

        if (!$tujuanAsalData || !$tujuanTujuanData) {
            return response()->json(['error' => 'Data tujuan tidak ditemukan'], 400);
        }

        // Use the `nama_tujuan` field for mapping — `kota` column doesn't exist in the current schema.
        // 1 Digit Tujuan Asal (berdasarkan nama_tujuan)
        $kotaAsal = $tujuanAsalData->nama_tujuan ?? '';
        $kodeAsal = $kotaCodes[$kotaAsal] ?? strtoupper(substr($kotaAsal, 0, 1));

        // 1 Digit Tujuan Kirim (berdasarkan nama_tujuan)
        $kotaTujuan = $tujuanTujuanData->nama_tujuan ?? '';
        $kodeTujuan = $kotaCodes[$kotaTujuan] ?? strtoupper(substr($kotaTujuan, 0, 1));

        // 2 Digit Tahun
        $tahun = date('y');

        // Generate voyage number: nickname(2) + noUrut(2) + kodeAsal(1) + kodeTujuan(1) + tahun(2)
        $voyageNumber = "{$nicknameKapal}{$noUrut}{$kodeAsal}{$kodeTujuan}{$tahun}";

        return response()->json(['voyage_number' => $voyageNumber]);
    }

    /**
     * Approve pergerakan kapal
     */
    public function approve(PergerakanKapal $pergerakanKapal)
    {
        $pergerakanKapal->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->name,
            'approved_at' => now()
        ]);

        return redirect()->route('pergerakan-kapal.index')
                        ->with('success', 'Data pergerakan kapal berhasil disetujui.');
    }

    /**
     * Print pergerakan kapal report
     */
    public function print(Request $request)
    {
        $query = PergerakanKapal::query();

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('kapal')) {
            $query->byKapal($request->kapal);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $pergerakanKapals = $query->orderBy('tanggal_sandar', 'desc')->get();

        return view('pergerakan-kapal.print', compact('pergerakanKapals'));
    }

    /**
     * Export pergerakan kapal data
     */
    public function export(Request $request)
    {
        $query = PergerakanKapal::query();

        // Apply filters if provided
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('kapal')) {
            $query->byKapal($request->kapal);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $pergerakanKapals = $query->orderBy('tanggal_sandar', 'desc')->get();

        // Export to CSV
        $filename = 'pergerakan_kapal_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($pergerakanKapals) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Nama Kapal',
                'Kapten',
                'Voyage',
                'Pelabuhan Asal',
                'Pelabuhan Tujuan',
                'Tanggal Sandar',
                'Tanggal Labuh',
                'Tanggal Berangkat',
                'Status',
                'Keterangan'
            ]);

            // Data rows
            foreach ($pergerakanKapals as $item) {
                fputcsv($file, [
                    $item->nama_kapal,
                    $item->kapten,
                    $item->voyage,
                    $item->tujuan_asal,
                    $item->tujuan_tujuan,
                    $item->tanggal_sandar ? $item->tanggal_sandar->format('Y-m-d') : '',
                    $item->tanggal_labuh ? $item->tanggal_labuh->format('Y-m-d') : '',
                    $item->tanggal_berangkat ? $item->tanggal_berangkat->format('Y-m-d') : '',
                    $item->status,
                    $item->keterangan
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
