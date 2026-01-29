<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\Cabang;
use App\Models\KontainerPerjalanan;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\HistoryKontainer;

class CheckpointKontainerKeluarController extends Controller
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
        $this->authorize('checkpoint-kontainer-keluar-view');

        // Get all cabangs from database
        $cabangs = Cabang::orderBy('nama_cabang')->get();

        return view('checkpoint-kontainer-keluar.index', compact('cabangs'));
    }

    /**
     * Display checkpoint page for specific branch (warehouse selection)
     */
    public function checkpoint($cabangSlug)
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        // Map slug to cabang name
        $cabangMap = [
            'jakarta' => 'Jakarta',
            'batam' => 'Batam',
            'tanjung-pinang' => 'Tanjung Pinang',
        ];

        $cabangNama = $cabangMap[$cabangSlug] ?? null;

        if (!$cabangNama) {
            return redirect()->route('checkpoint-kontainer-keluar.index')
                ->with('error', 'Cabang tidak ditemukan');
        }

        // Get gudangs by lokasi (cabang)
        $gudangs = \App\Models\Gudang::where('lokasi', $cabangNama)
            ->where('status', 'aktif')
            ->orderBy('nama_gudang')
            ->get();

        return view('checkpoint-kontainer-keluar.checkpoint', compact('gudangs', 'cabangNama', 'cabangSlug'));
    }

    /**
     * Display surat jalan list for specific gudang
     */
    public function showSuratJalan($cabangSlug, $gudangId)
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        // Map slug to cabang name
        $cabangMap = [
            'jakarta' => 'Jakarta',
            'batam' => 'Batam',
            'tanjung-pinang' => 'Tanjung Pinang',
        ];

        $cabangNama = $cabangMap[$cabangSlug] ?? null;

        if (!$cabangNama) {
            return redirect()->route('checkpoint-kontainer-keluar.index')
                ->with('error', 'Cabang tidak ditemukan');
        }

        $gudang = \App\Models\Gudang::findOrFail($gudangId);

        // Get kontainers by gudangs_id
        $kontainers = \App\Models\Kontainer::where('gudangs_id', $gudangId)
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Get stock_kontainers by gudangs_id
        $stockKontainers = \App\Models\StockKontainer::where('gudangs_id', $gudangId)
            ->orderBy('nomor_seri_gabungan', 'asc')
            ->get();

        // Get surat jalans that haven't received tanda terima yet
        $suratJalans = SuratJalan::whereNull('tanggal_tanda_terima')
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->orderBy('no_surat_jalan', 'desc')
            ->get();

        // Get all active gudangs for tujuan dropdown
        $gudangs = \App\Models\Gudang::where('status', 'aktif')
            ->orderBy('lokasi')
            ->orderBy('nama_gudang')
            ->get();

        return view('checkpoint-kontainer-keluar.surat-jalan', compact('kontainers', 'stockKontainers', 'cabangNama', 'cabangSlug', 'gudang', 'suratJalans', 'gudangs'));
    }

    /**
     * Display history of checkpoint kontainer keluar
     */
    public function history(Request $request)
    {
        $this->authorize('checkpoint-kontainer-keluar-view');

        $query = SuratJalan::whereNotNull('tanggal_checkpoint')
            ->orderBy('tanggal_checkpoint', 'desc');

        // Filter by date range if provided
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_checkpoint', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_checkpoint', '<=', $request->tanggal_sampai);
        }

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        $suratJalans = $query->paginate(20);

        return view('checkpoint-kontainer-keluar.history', compact('suratJalans'));
    }

    /**
     * Process kontainer keluar
     */
    public function processKeluar(Request $request, $suratJalanId)
    {
        $this->authorize('checkpoint-kontainer-keluar-create');

        $request->validate([
            'tanggal_checkpoint' => 'required|date',
            'catatan_checkpoint' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $suratJalan = SuratJalan::findOrFail($suratJalanId);

            // Update surat jalan
            $suratJalan->update([
                'tanggal_checkpoint' => $request->tanggal_checkpoint,
                'catatan_checkpoint' => $request->catatan_checkpoint,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kontainer berhasil keluar dari checkpoint'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses kontainer keluar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process bulk kontainer keluar
     */
    public function bulkKeluar(Request $request)
    {
        $this->authorize('checkpoint-kontainer-keluar-create');

        $request->validate([
            'surat_jalan_ids' => 'required|array',
            'surat_jalan_ids.*' => 'exists:surat_jalans,id',
            'tanggal_checkpoint' => 'required|date',
            'catatan_checkpoint' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $updated = SuratJalan::whereIn('id', $request->surat_jalan_ids)
                ->update([
                    'tanggal_checkpoint' => $request->tanggal_checkpoint,
                    'catatan_checkpoint' => $request->catatan_checkpoint,
                ]);

            DB::commit();

            return redirect()->back()->with('success', "Berhasil memproses {$updated} kontainer keluar");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memproses kontainer keluar: ' . $e->getMessage());
        }
    }

    /**
     * Cancel kontainer keluar
     */
    public function cancelKeluar($suratJalanId)
    {
        $this->authorize('checkpoint-kontainer-keluar-delete');

        try {
            DB::beginTransaction();

            $suratJalan = SuratJalan::findOrFail($suratJalanId);

            // Reset checkpoint data
            $suratJalan->update([
                'tanggal_checkpoint' => null,
                'catatan_checkpoint' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkpoint kontainer keluar berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan checkpoint: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process kirim kontainer - saves to kontainer_perjalanans
     */
    public function kirimKontainer(Request $request)
    {
        $this->authorize('checkpoint-kontainer-keluar-create');

        $request->validate([
            'tipe_data' => 'required|in:kontainer,stock',
            'kontainer_id' => 'required|integer',
            'gudangs_id' => 'required|exists:gudangs,id',
            'tujuan' => 'required|string|max:255',
            'tanggal_kirim' => 'required|date',
            'nomor_surat_jalan' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Get kontainer data based on tipe_data
            $kontainerData = null;
            $kontainerRecord = null;
            $stockRecord = null;
            
            if ($request->tipe_data === 'kontainer') {
                $kontainerRecord = Kontainer::findOrFail($request->kontainer_id);
                $kontainerData = [
                    'no_kontainer' => $kontainerRecord->nomor_seri_gabungan,
                    'ukuran' => $kontainerRecord->ukuran,
                    'tipe_kontainer' => $kontainerRecord->tipe_kontainer,
                ];
            } else {
                $stockRecord = StockKontainer::findOrFail($request->kontainer_id);
                $kontainerData = [
                    'no_kontainer' => $stockRecord->nomor_seri_gabungan,
                    'ukuran' => $stockRecord->ukuran ?? '-',
                    'tipe_kontainer' => $stockRecord->tipe_kontainer ?? '-',
                ];
            }

            // Find surat jalan if nomor provided
            $suratJalanId = null;
            if ($request->filled('nomor_surat_jalan')) {
                $suratJalan = SuratJalan::where('no_surat_jalan', $request->nomor_surat_jalan)->first();
                if ($suratJalan) {
                    $suratJalanId = $suratJalan->id;
                    
                    // Update surat jalan dengan nomor kontainer, ukuran, dan waktu keluar
                    $suratJalan->update([
                        'no_kontainer' => $kontainerData['no_kontainer'],
                        'ukuran' => $kontainerData['ukuran'],
                        'waktu_keluar' => Carbon::parse($request->tanggal_kirim),
                        'tujuan_pengiriman' => $request->tujuan,
                    ]);
                }
            }

            // Create kontainer perjalanan record
            KontainerPerjalanan::create([
                'surat_jalan_id' => $suratJalanId,
                'no_kontainer' => $kontainerData['no_kontainer'],
                'no_surat_jalan' => $request->nomor_surat_jalan,
                'ukuran' => $kontainerData['ukuran'],
                'tipe_kontainer' => $kontainerData['tipe_kontainer'],
                'tujuan_pengiriman' => $request->tujuan,
                'waktu_keluar' => Carbon::parse($request->tanggal_kirim),
                'status' => 'dalam_perjalanan',
                'catatan' => $request->keterangan,
                'created_by' => Auth::id(),
            ]);

            // Update gudangs_id to null (dalam perjalanan)
            if ($kontainerRecord) {
                $kontainerRecord->update(['gudangs_id' => null]);
            } elseif ($stockRecord) {
                $stockRecord->update(['gudangs_id' => null]);
            }

            // Create History Entry
            HistoryKontainer::create([
                'nomor_kontainer' => $kontainerData['no_kontainer'],
                'tipe_kontainer' => ($kontainerRecord) ? 'kontainer' : 'stock',
                'jenis_kegiatan' => 'Keluar',
                'tanggal_kegiatan' => Carbon::parse($request->tanggal_kirim),
                'gudang_id' => $request->gudangs_id,
                'keterangan' => 'Dikirim ke: ' . $request->tujuan . ($request->keterangan ? '. Ket: ' . $request->keterangan : ''),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Kontainer berhasil dikirim dan masuk ke Kontainer Dalam Perjalanan');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal mengirim kontainer: ' . $e->getMessage());
        }
    }

    /**
     * Store pengembalian kontainer sewa
     */
    public function storePengembalian(Request $request)
    {
        $this->authorize('checkpoint-kontainer-keluar-create');

        $request->validate([
            'gudangs_id' => 'required|exists:gudangs,id',
            'kontainer_id' => 'required',
            'kontainer_tipe' => 'required|in:kontainer,stock',
            'tanggal_pengembalian' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Get kontainer record based on type
            if ($request->kontainer_tipe === 'kontainer') {
                $kontainer = Kontainer::findOrFail($request->kontainer_id);
            } else {
                $kontainer = StockKontainer::findOrFail($request->kontainer_id);
            }

            // Update kontainer - set gudangs_id to null (dikembalikan)
            $kontainer->update([
                'gudangs_id' => null,
                'keterangan' => ($kontainer->keterangan ? $kontainer->keterangan . ' | ' : '') . 
                               'Dikembalikan pada ' . Carbon::parse($request->tanggal_pengembalian)->format('d/m/Y') . 
                               ($request->keterangan ? ': ' . $request->keterangan : ''),
            ]);

            // Create History Entry
            HistoryKontainer::create([
                'nomor_kontainer' => $kontainer->nomor_seri_gabungan,
                'tipe_kontainer' => $request->kontainer_tipe, // 'kontainer' or 'stock' from request
                'jenis_kegiatan' => 'Keluar',
                'tanggal_kegiatan' => Carbon::parse($request->tanggal_pengembalian),
                'gudang_id' => $request->gudangs_id,
                'keterangan' => 'Pengembalian Kontainer. ' . ($request->keterangan ?? ''),
                'created_by' => Auth::id(),
            ]);

            // Optional: Create a log or history record here if needed
            // Example: PengembalianKontainerLog::create([...]);

            DB::commit();

            return redirect()->back()->with('success', 'Kontainer berhasil dikembalikan');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal mengembalikan kontainer: ' . $e->getMessage());
        }
    }
}
