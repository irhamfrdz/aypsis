<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\Cabang;
use App\Models\Gudang;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\KontainerPerjalanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckpointKontainerMasukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display branch selection page
     */
    public function index()
    {
        $this->authorize('checkpoint-kontainer-masuk-view');

        // Get all cabangs from database
        $cabangs = Cabang::orderBy('nama_cabang')->get();

        return view('checkpoint-kontainer-masuk.index', compact('cabangs'));
    }

    /**
     * Display checkpoint page for specific branch (warehouse selection)
     */
    public function checkpoint($cabangSlug)
    {
        $this->authorize('checkpoint-kontainer-masuk-view');

        // Map slug to cabang name
        $cabangMap = [
            'jakarta' => 'Jakarta',
            'batam' => 'Batam',
            'tanjung-pinang' => 'Tanjung Pinang',
        ];

        $cabangNama = $cabangMap[$cabangSlug] ?? null;

        if (!$cabangNama) {
            return redirect()->route('checkpoint-kontainer-masuk.index')
                ->with('error', 'Cabang tidak ditemukan');
        }

        // Get gudangs by lokasi (cabang)
        $gudangs = Gudang::where('lokasi', $cabangNama)
            ->where('status', 'aktif')
            ->orderBy('nama_gudang')
            ->get();

        return view('checkpoint-kontainer-masuk.checkpoint', compact('gudangs', 'cabangNama', 'cabangSlug'));
    }

    /**
     * Display list of containers to receive at specific gudang
     */
    public function showKontainer($cabangSlug, $gudangId)
    {
        $this->authorize('checkpoint-kontainer-masuk-view');

        // Map slug to cabang name
        $cabangMap = [
            'jakarta' => 'Jakarta',
            'batam' => 'Batam',
            'tanjung-pinang' => 'Tanjung Pinang',
        ];

        $cabangNama = $cabangMap[$cabangSlug] ?? null;

        if (!$cabangNama) {
            return redirect()->route('checkpoint-kontainer-masuk.index')
                ->with('error', 'Cabang tidak ditemukan');
        }

        $gudang = Gudang::findOrFail($gudangId);

        // Get kontainers yang sedang dalam perjalanan menuju gudang ini
        // Dari tabel kontainer_perjalanans yang tujuan_pengiriman sesuai dengan nama gudang
        $kontainersDalamPerjalanan = KontainerPerjalanan::with('suratJalan')
            ->where('status', 'dalam_perjalanan')
            ->where('tujuan_pengiriman', $gudang->nama_gudang)
            ->orderBy('waktu_keluar', 'desc')
            ->get();

        return view('checkpoint-kontainer-masuk.kontainer', compact('kontainersDalamPerjalanan', 'cabangNama', 'cabangSlug', 'gudang'));
    }

    /**
     * Process kontainer masuk (arrival)
     */
    public function processMasuk(Request $request, $kontainerPerjalananId)
    {
        $this->authorize('checkpoint-kontainer-masuk-create');

        $request->validate([
            'nomor_kontainer' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'waktu_masuk' => 'required',
            'catatan_masuk' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Get kontainer perjalanan
            $kontainerPerjalanan = KontainerPerjalanan::findOrFail($kontainerPerjalananId);
            
            // Combine date and time
            $waktuTiba = Carbon::parse($request->tanggal_masuk . ' ' . $request->waktu_masuk);

            // Update kontainer perjalanan status to 'sampai_tujuan'
            $kontainerPerjalanan->update([
                'waktu_tiba_aktual' => $waktuTiba,
                'status' => 'sampai_tujuan',
                'catatan_tiba' => $request->catatan_masuk,
                'updated_by' => Auth::id(),
            ]);

            // Update surat jalan if exists
            if ($kontainerPerjalanan->suratJalan) {
                $kontainerPerjalanan->suratJalan->update([
                    'waktu_masuk' => $waktuTiba,
                    'catatan_masuk' => $request->catatan_masuk,
                ]);
            }

            // Find gudang by tujuan_pengiriman
            $gudang = Gudang::where('nama_gudang', $kontainerPerjalanan->tujuan_pengiriman)->first();
            
            if ($gudang) {
                // Update gudangs_id on kontainers or stock_kontainers
                $kontainer = Kontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)->first();
                if ($kontainer) {
                    $kontainer->update(['gudangs_id' => $gudang->id]);
                } else {
                    $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)->first();
                    if ($stockKontainer) {
                        $stockKontainer->update(['gudangs_id' => $gudang->id]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Kontainer berhasil di-checkpoint masuk dan ditempatkan di gudang');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal checkpoint kontainer masuk: ' . $e->getMessage());
        }
    }

    /**
     * Process manual kontainer masuk (without kontainer_perjalanan record)
     */
    public function manualMasuk(Request $request, $cabangSlug, $gudangId)
    {
        $this->authorize('checkpoint-kontainer-masuk-create');

        $request->validate([
            'nomor_kontainer' => 'required|string',
            'ukuran' => 'nullable|string',
            'tipe_kontainer' => 'nullable|string',
            'no_surat_jalan' => 'nullable|string',
            'tanggal_masuk' => 'required|date',
            'waktu_masuk' => 'required',
            'catatan_masuk' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $gudang = Gudang::findOrFail($gudangId);
            
            // Combine date and time
            $waktuTiba = Carbon::parse($request->tanggal_masuk . ' ' . $request->waktu_masuk);

            // Create kontainer perjalanan record untuk tracking
            KontainerPerjalanan::create([
                'surat_jalan_id' => null,
                'no_kontainer' => $request->nomor_kontainer,
                'no_surat_jalan' => $request->no_surat_jalan,
                'ukuran' => $request->ukuran,
                'tipe_kontainer' => $request->tipe_kontainer,
                'tujuan_pengiriman' => $gudang->nama_gudang,
                'waktu_keluar' => $waktuTiba, // Set waktu keluar = waktu tiba untuk entry manual
                'waktu_tiba_aktual' => $waktuTiba,
                'status' => 'sampai_tujuan',
                'catatan_tiba' => $request->catatan_masuk,
                'created_by' => Auth::id(),
            ]);

            // Update gudangs_id on kontainers or stock_kontainers
            $kontainer = Kontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)->first();
            if ($kontainer) {
                $kontainer->update(['gudangs_id' => $gudang->id]);
            } else {
                $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)->first();
                if ($stockKontainer) {
                    $stockKontainer->update(['gudangs_id' => $gudang->id]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Kontainer manual berhasil di-checkpoint masuk dan ditempatkan di gudang');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal checkpoint kontainer masuk manual: ' . $e->getMessage());
        }
    }

    /**
     * Bulk process kontainer masuk
     */
    public function bulkMasuk(Request $request)
    {
        $this->authorize('checkpoint-kontainer-masuk-create');

        $request->validate([
            'surat_jalan_ids' => 'required|array',
            'surat_jalan_ids.*' => 'exists:surat_jalans,id',
            'tanggal_masuk' => 'required|date',
            'waktu_masuk' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $waktuMasuk = $request->tanggal_masuk . ' ' . $request->waktu_masuk;

            foreach ($request->surat_jalan_ids as $suratJalanId) {
                $suratJalan = SuratJalan::findOrFail($suratJalanId);
                $suratJalan->update([
                    'waktu_masuk' => $waktuMasuk,
                    'catatan_masuk' => $request->catatan_masuk,
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', count($request->surat_jalan_ids) . ' kontainer berhasil di-checkpoint masuk');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal bulk checkpoint: ' . $e->getMessage());
        }
    }

    /**
     * Display history of checkpoint kontainer masuk
     */
    public function history(Request $request)
    {
        $this->authorize('checkpoint-kontainer-masuk-view');

        $query = SuratJalan::whereNotNull('waktu_masuk')
            ->orderBy('waktu_masuk', 'desc');

        // Filter by date range if provided
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('waktu_masuk', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('waktu_masuk', '<=', $request->sampai_tanggal);
        }

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%")
                    ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        $history = $query->paginate(20);

        return view('checkpoint-kontainer-masuk.history', compact('history'));
    }

    /**
     * Cancel checkpoint masuk
     */
    public function cancelMasuk($suratJalanId)
    {
        $this->authorize('checkpoint-kontainer-masuk-delete');

        try {
            DB::beginTransaction();

            $suratJalan = SuratJalan::findOrFail($suratJalanId);
            
            $suratJalan->update([
                'waktu_masuk' => null,
                'catatan_masuk' => null,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Checkpoint masuk berhasil dibatalkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan checkpoint: ' . $e->getMessage());
        }
    }
}
