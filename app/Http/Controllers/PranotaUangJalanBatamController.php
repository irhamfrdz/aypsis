<?php

namespace App\Http\Controllers;

use App\Models\UangJalanBatam;
use App\Models\PranotaUangJalanBatam;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\PranotaUangJalanBatamExport; // Will create if needed

class PranotaUangJalanBatamController extends Controller
{
    /**
     * Display a listing of pranota uang jalan batam.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permission - using existing logic style
        if (!$this->hasPermission($user, 'pranota-uang-jalan-batam-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota uang jalan batam.');
        }

        // Build query with filters
        $query = PranotaUangJalanBatam::with(['uangJalanBatams'])
            ->withCount('uangJalanBatams');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhereHas('uangJalanBatams', function($sq) use ($search) {
                      $sq->where('nomor_uang_jalan', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Pagination
        $pranotaUangJalanBatams = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Statistics
        $stats = [
            'total' => PranotaUangJalanBatam::count(),
            'this_month' => PranotaUangJalanBatam::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'unpaid' => PranotaUangJalanBatam::where('status_pembayaran', 'unpaid')->count(),
            'paid' => PranotaUangJalanBatam::where('status_pembayaran', 'paid')->count(),
        ];

        return view('pranota-uang-jalan-batam.index', compact('pranotaUangJalanBatams', 'stats'));
    }

    /**
     * Show form for creating new pranota.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user, 'pranota-uang-jalan-batam-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota uang jalan batam.');
        }

        // Get available uang jalans (not in any pranota and not cancelled/paid directly)
        $availableUangJalanBatams = UangJalanBatam::with(['suratJalanBatam'])
            ->whereDoesntHave('pranotaUangJalanBatams')
            ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
            ->orderBy('tanggal_uang_jalan', 'desc')
            ->get();

        return view('pranota-uang-jalan-batam.create', compact('availableUangJalanBatams'));
    }

    /**
     * Store new pranota.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user, 'pranota-uang-jalan-batam-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota uang jalan batam.');
        }

        $request->validate([
            'uang_jalan_ids' => 'required|array|min:1',
            'uang_jalan_ids.*' => 'exists:uang_jalan_batams,id',
            'tanggal_pranota' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'penyesuaian' => 'nullable|numeric',
            'keterangan_penyesuaian' => 'nullable|string|max:500',
        ]);

        $selectedUangJalans = UangJalanBatam::whereIn('id', $request->uang_jalan_ids)
            ->whereDoesntHave('pranotaUangJalanBatams')
            ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
            ->get();

        if ($selectedUangJalans->count() !== count($request->uang_jalan_ids)) {
            return back()->with('error', 'Beberapa uang jalan yang dipilih tidak tersedia atau sudah masuk pranota.')->withInput();
        }

        DB::beginTransaction();
        try {
            $nomorPranota = $this->generateNomorPranota();
            $totalAmount = $selectedUangJalans->sum('jumlah_total');

            $pranota = PranotaUangJalanBatam::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_tagihan' => now()->format('Y-m'),
                'jumlah_uang_jalan' => count($request->uang_jalan_ids),
                'total_amount' => $totalAmount,
                'penyesuaian' => $request->penyesuaian ?? 0,
                'keterangan_penyesuaian' => $request->keterangan_penyesuaian,
                'status_pembayaran' => 'unpaid',
                'catatan' => $request->keterangan,
                'created_by' => $user->id,
            ]);

            // Track IDs for easier updates
            $ids = $selectedUangJalans->pluck('id')->toArray();
            
            // Attach
            $pranota->uangJalanBatams()->attach($ids);

            // Update status
            UangJalanBatam::whereIn('id', $ids)->update(['status' => 'sudah_masuk_pranota']);

            DB::commit();
            return redirect()->route('pranota-uang-jalan-batam.index')
                ->with('success', 'Pranota Uang Jalan Batam berhasil dibuat: ' . $nomorPranota);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pranota ujal batam: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pranota: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show show page.
     */
    public function show(PranotaUangJalanBatam $pranotaUangJalanBatam)
    {
        if (!$this->hasPermission(Auth::user(), 'pranota-uang-jalan-batam-view')) {
            abort(403);
        }

        $pranotaUangJalanBatam->load(['uangJalanBatams.suratJalanBatam', 'creator']);
        return view('pranota-uang-jalan-batam.show', compact('pranotaUangJalanBatam'));
    }

    /**
     * Print pranota.
     */
    public function print(PranotaUangJalanBatam $pranotaUangJalanBatam)
    {
        if (!$this->hasPermission(Auth::user(), 'pranota-uang-jalan-batam-view')) {
            abort(403);
        }

        $pranotaUangJalanBatam->load(['uangJalanBatams.suratJalanBatam', 'creator']);
        return view('pranota-uang-jalan-batam.print', compact('pranotaUangJalanBatam'));
    }

    /**
     * Delete pranota.
     */
    public function destroy(PranotaUangJalanBatam $pranotaUangJalanBatam)
    {
        if (!$this->hasPermission(Auth::user(), 'pranota-uang-jalan-batam-delete')) {
            abort(403);
        }

        if ($pranotaUangJalanBatam->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Restore statuses
            $pranotaUangJalanBatam->uangJalanBatams()->update(['status' => 'belum_masuk_pranota']);
            $pranotaUangJalanBatam->uangJalanBatams()->detach();
            $pranotaUangJalanBatam->delete();

            DB::commit();
            return redirect()->route('pranota-uang-jalan-batam.index')->with('success', 'Pranota berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pranota.');
        }
    }

    /**
     * Generate number.
     */
    private function generateNomorPranota()
    {
        $bulan = date('m');
        $tahun = date('y');

        $nomorTerakhir = NomorTerakhir::where('modul', 'PUJBTM')->lockForUpdate()->first();

        if (!$nomorTerakhir) {
            $nomorTerakhir = NomorTerakhir::create(['modul' => 'PUJBTM', 'nomor_terakhir' => 0, 'keterangan' => 'Pranota Uang Jalan Batam']);
        }

        $next = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->update(['nomor_terakhir' => $next]);

        return "PUJBTM-{$bulan}{$tahun}-" . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Permission checker.
     */
    private function hasPermission($user, $permission)
    {
        if (!$user) return false;
        if (in_array($user->role, ["admin", "user_admin"])) return true;

        return DB::table("user_permissions")
            ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
            ->where("user_permissions.user_id", $user->id)
            ->where("permissions.name", $permission)
            ->exists();
    }
}
