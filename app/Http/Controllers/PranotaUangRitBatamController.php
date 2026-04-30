<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBatam;
use App\Models\PranotaUangRitBatam;
use App\Models\PranotaUangRitBatamItem;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PranotaUangRitBatamController extends Controller
{
    /**
     * Display a listing of pranota uang rit batam.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build query with filters
        $query = PranotaUangRitBatam::with(['suratJalanBatams'])
            ->withCount('suratJalanBatams');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhere('supir_nama', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pranota', [$request->start_date, $request->end_date]);
        }

        // Pagination
        $pranotaUangRitBatams = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('pranota-uang-rit-batam.index', compact('pranotaUangRitBatams'));
    }

    /**
     * Show the date selection page for creating a new pranota.
     */
    public function selectDate(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        return view('pranota-uang-rit-batam.select-date', compact('start_date', 'end_date'));
    }

    /**
     * Show form for creating new pranota.
     */
    public function create(Request $request)
    {
        // Default to last 30 days if no date range is provided
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));

        // Validate date inputs
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startCheck = \Carbon\Carbon::parse($startDate)->startOfDay();
                $endCheck = \Carbon\Carbon::parse($endDate)->endOfDay();
                if ($startCheck->gt($endCheck)) {
                    return redirect()->route('pranota-uang-rit-batam.select-date')->withInput()->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
                }
            } catch (\Exception $e) {
                return redirect()->route('pranota-uang-rit-batam.select-date')->withInput()->with('error', 'Format tanggal tidak valid.');
            }
        }

        // Get available surat jalans batam (not in any pranota and rit status is not paid)
        $query = SuratJalanBatam::whereIn('status', ['active', 'completed', 'sudah_checkpoint'])
            ->where(function($q) {
                $q->whereNull('status_pembayaran_uang_rit')
                  ->orWhere('status_pembayaran_uang_rit', 'belum_dibayar')
                  ->orWhere('status_pembayaran_uang_rit', 'belum_masuk_pranota');
            });

        // Apply date range filter
        if ($startDate && $endDate) {
            try {
                $startDateObj = \Carbon\Carbon::parse($startDate)->startOfDay();
                $endDateObj = \Carbon\Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('tanggal_surat_jalan', [$startDateObj, $endDateObj]);
            } catch (\Exception $e) {
                // If parsing fails, don't apply date filter
            }
        }

        $availableSuratJalans = $query->orderBy('tanggal_surat_jalan', 'desc')->get();

        $viewStartDate = $startDate;
        $viewEndDate = $endDate;

        return view('pranota-uang-rit-batam.create', compact('availableSuratJalans', 'viewStartDate', 'viewEndDate'));
    }

    /**
     * Store new pranota.
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_ids' => 'required|array|min:1',
            'surat_jalan_ids.*' => 'exists:surat_jalan_batams,id',
            'tanggal_pranota' => 'required|date',
            'supir_nama' => 'required|string',
            'catatan' => 'nullable|string|max:500',
            'penyesuaian' => 'nullable|numeric',
        ]);

        $selectedSJs = SuratJalanBatam::whereIn('id', $request->surat_jalan_ids)->get();

        DB::beginTransaction();
        try {
            $nomorPranota = $this->generateNomorPranota();
            
            // For now, we assume Uang Rit is taken from the 'rit' field or a default value
            // Adjust this logic if there's a specific calculation
            $totalRit = 0;
            foreach ($selectedSJs as $sj) {
                // Assuming 'rit' field contains the amount or we use a fixed amount
                $totalRit += is_numeric($sj->rit) ? (float)$sj->rit : 0;
            }

            $pranota = PranotaUangRitBatam::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'supir_nama' => $request->supir_nama,
                'total_rit' => $totalRit,
                'penyesuaian' => $request->penyesuaian ?? 0,
                'total_amount' => $totalRit + ($request->penyesuaian ?? 0),
                'status_pembayaran' => 'unpaid',
                'catatan' => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            foreach ($selectedSJs as $sj) {
                PranotaUangRitBatamItem::create([
                    'pranota_uang_rit_batam_id' => $pranota->id,
                    'surat_jalan_batam_id' => $sj->id,
                    'uang_rit' => is_numeric($sj->rit) ? (float)$sj->rit : 0,
                ]);

                // Update SuratJalan status
                $sj->update([
                    'status_pembayaran_uang_rit' => 'sudah_masuk_pranota'
                ]);
            }

            DB::commit();
            return redirect()->route('pranota-uang-rit-batam.index')
                ->with('success', 'Pranota Uang Rit Batam berhasil dibuat: ' . $nomorPranota);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pranota rit batam: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pranota: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show detail page.
     */
    public function show($id)
    {
        $pranota = PranotaUangRitBatam::with(['suratJalanBatams', 'creator'])->findOrFail($id);
        return view('pranota-uang-rit-batam.show', compact('pranota'));
    }

    /**
     * Delete pranota.
     */
    public function destroy($id)
    {
        $pranota = PranotaUangRitBatam::findOrFail($id);

        if ($pranota->status_pembayaran === 'paid') {
            return back()->with('error', 'Pranota yang sudah dibayar tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Restore SuratJalan statuses
            foreach ($pranota->suratJalanBatams as $sj) {
                $sj->update(['status_pembayaran_uang_rit' => 'belum_masuk_pranota']);
            }

            $pranota->delete();

            DB::commit();
            return redirect()->route('pranota-uang-rit-batam.index')->with('success', 'Pranota berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pranota.');
        }
    }

    /**
     * Generate number using standard pattern.
     */
    private function generateNomorPranota()
    {
        $bulan = date('m');
        $tahun = date('y');

        $nomorTerakhir = NomorTerakhir::where('modul', 'PURBTM')->lockForUpdate()->first();

        if (!$nomorTerakhir) {
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'PURBTM', 
                'nomor_terakhir' => 0, 
                'keterangan' => 'Pranota Uang Rit Batam'
            ]);
        }

        $next = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->update(['nomor_terakhir' => $next]);

        return "PURBTM-{$bulan}{$tahun}-" . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
