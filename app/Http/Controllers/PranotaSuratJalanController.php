<?php

namespace App\Http\Controllers;

use App\Models\UangJalan;
use App\Models\PranotaUangJalan;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PranotaUangJalanExport;

class PranotaSuratJalanController extends Controller
{
    /**
     * Display a listing of pranota uang jalan.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota uang jalan.');
        }

        // Build query with filters
        $query = PranotaUangJalan::with(['uangJalans'])
            ->withCount('uangJalans');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhereHas('uangJalans', function($sq) use ($search) {
                      $sq->where('nomor_uang_jalan', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter - menggunakan status_pembayaran
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Get pranota uang jalan with pagination
        $pranotaUangJalans = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Get statistics - menggunakan status_pembayaran
        $stats = [
            'total' => PranotaUangJalan::count(),
            'this_month' => PranotaUangJalan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'unpaid' => PranotaUangJalan::where('status_pembayaran', 'unpaid')->count(),
            'paid' => PranotaUangJalan::where('status_pembayaran', 'paid')->count(),
        ];

        return view('pranota-uang-jalan.index', compact('pranotaUangJalans', 'stats'));
    }

    /**
     * Export pranota uang jalan listing to Excel with current filters
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-export')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor pranota uang jalan.');
        }

        try {
            $filters = $request->only(['search', 'status']);
            $fileName = 'pranota_uang_jalan_export_' . date('Ymd_His') . '.xlsx';
            $export = new PranotaUangJalanExport($filters, []);
            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            Log::error('Error exporting pranota uang jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal export pranota uang jalan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new pranota uang jalan.
     */
    public function create()
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota uang jalan.');
        }

        // Get uang jalan yang belum ada pranota (termasuk bongkaran)
        $availableUangJalans = UangJalan::with([
                'suratJalan.supirKaryawan', 
                'suratJalan.kenekKaryawan',
                'suratJalanBongkaran.supirKaryawan',
                'suratJalanBongkaran.kenekKaryawan'
            ])
            ->whereDoesntHave('pranotaUangJalan')
            ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
            ->orderBy('tanggal_uang_jalan', 'desc')
            ->get();

        return view('pranota-uang-jalan.create', compact('availableUangJalans'));
    }

    /**
     * Store a newly created pranota uang jalan in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota uang jalan.');
        }

        // Validation
        $request->validate([
            'uang_jalan_ids' => 'required|array|min:1',
            'uang_jalan_ids.*' => 'exists:uang_jalans,id',
            'tanggal_pranota' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'penyesuaian' => 'nullable|numeric',
            'penyesuaian_amount' => 'nullable|numeric|min:0',
            'keterangan_penyesuaian' => 'nullable|string|max:500',
        ]);

        // Additional validation: Ensure all selected uang jalans are available for pranota
        $selectedUangJalans = UangJalan::whereIn('id', $request->uang_jalan_ids)
            ->whereDoesntHave('pranotaUangJalan')
            ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
            ->get();

        if ($selectedUangJalans->count() !== count($request->uang_jalan_ids)) {
            return redirect()->back()
                ->withErrors(['uang_jalan_ids' => 'Beberapa uang jalan yang dipilih tidak tersedia atau sudah masuk pranota.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate nomor pranota
            $nomorPranota = $this->generateNomorPranota();
            $date = Carbon::now();

            // Calculate total from selected uang jalans
            $totalAmount = 0;
            foreach ($selectedUangJalans as $uangJalan) {
                $totalAmount += $uangJalan->jumlah_total ?? 0;
            }

            // Create pranota uang jalan
            $pranotaUangJalan = PranotaUangJalan::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_tagihan' => $date->format('Y-m'),
                'jumlah_uang_jalan' => count($request->uang_jalan_ids),
                'total_amount' => $totalAmount,
                'penyesuaian' => $request->penyesuaian ?? 0,
                'keterangan_penyesuaian' => $request->keterangan_penyesuaian,
                'status_pembayaran' => 'unpaid',
                'catatan' => $request->keterangan,
                'created_by' => $user->id,
            ]);

            // Update status uang jalans: belum_masuk_pranota -> sudah_masuk_pranota
            UangJalan::whereIn('id', $request->uang_jalan_ids)
                ->update(['status' => 'sudah_masuk_pranota']);

            // Attach uang jalans to pranota
            $pranotaUangJalan->uangJalans()->attach($request->uang_jalan_ids);

            Log::info('Pranota uang jalan created', [
                'pranota_id' => $pranotaUangJalan->id,
                'nomor_pranota' => $nomorPranota,
                'uang_jalan_count' => count($request->uang_jalan_ids),
                'total_amount' => $totalAmount,
                'created_by' => $user->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-jalan.index')
                ->with('success', 'Pranota uang jalan berhasil dibuat dengan nomor: ' . $nomorPranota . ' dan status "Belum Dibayar". Status ' . count($request->uang_jalan_ids) . ' uang jalan telah diubah menjadi "Sudah Masuk Pranota".');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pranota uang jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pranota uang jalan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified pranota uang jalan.
     */
    public function show(PranotaUangJalan $pranotaUangJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota uang jalan.');
        }

        // Load relationships
        $pranotaUangJalan->load(['uangJalans.suratJalan', 'creator']);

        return view('pranota-uang-jalan.show', compact('pranotaUangJalan'));
    }

    /**
     * Print pranota uang jalan.
     */
    public function print(PranotaUangJalan $pranotaUangJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak pranota uang jalan.');
        }

        // Load relationships
        $pranotaUangJalan->load(['uangJalans.suratJalan.supirKaryawan', 'uangJalans.suratJalan.kenekKaryawan', 'creator']);

        return view('pranota-uang-jalan.print', compact('pranotaUangJalan'));
    }

    /**
     * Show the form for editing the specified pranota uang jalan.
     */
    public function edit(PranotaUangJalan $pranotaUangJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota uang jalan.');
        }

        // Only allow editing if status is unpaid
        if ($pranotaUangJalan->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat diubah.');
        }

        $pranotaUangJalan->load(['uangJalans']);

        return view('pranota-uang-jalan.edit', compact('pranotaUangJalan'));
    }

    /**
     * Update the specified pranota uang jalan in storage.
     */
    public function update(Request $request, PranotaUangJalan $pranotaUangJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota uang jalan.');
        }

        // Only allow updating if status is unpaid
        if ($pranotaUangJalan->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat diubah.');
        }

        // Validation
        $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_tagihan' => 'required|string|max:20',
            'catatan' => 'nullable|string|max:500',
            'penyesuaian' => 'nullable|numeric',
            'penyesuaian_amount' => 'nullable|numeric|min:0',
            'keterangan_penyesuaian' => 'nullable|string|max:500',
        ]);

        try {
            $pranotaUangJalan->update([
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_tagihan' => $request->periode_tagihan,
                'catatan' => $request->catatan,
                'penyesuaian' => $request->penyesuaian ?? 0,
                'keterangan_penyesuaian' => $request->keterangan_penyesuaian,
                'updated_by' => $user->id,
            ]);

            Log::info('Pranota uang jalan updated', [
                'pranota_id' => $pranotaUangJalan->id,
                'updated_by' => $user->name,
            ]);

            return redirect()->route('pranota-uang-jalan.show', $pranotaUangJalan)
                ->with('success', 'Pranota uang jalan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error updating pranota uang jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui pranota uang jalan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified pranota uang jalan from storage.
     */
    public function destroy(PranotaUangJalan $pranotaUangJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaUangJalanPermission($user, 'pranota-uang-jalan-delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pranota uang jalan.');
        }

        // Only allow deleting if status is unpaid
        if ($pranotaUangJalan->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Restore uang jalan status back to 'belum_masuk_pranota' so they can be included in new pranota
            $pranotaUangJalan->uangJalans()->update(['status' => 'belum_masuk_pranota']);

            // Detach uang jalans
            $pranotaUangJalan->uangJalans()->detach();

            // Delete pranota
            $pranotaUangJalan->delete();

            Log::info('Pranota uang jalan deleted', [
                'pranota_id' => $pranotaUangJalan->id,
                'deleted_by' => $user->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-jalan.index')
                ->with('success', 'Pranota uang jalan berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pranota uang jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pranota uang jalan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor pranota dengan format PUJ-MMYY-XXXXXX
     */
    private function generateNomorPranota()
    {
        $date = Carbon::now();
        $bulan = $date->format('m'); // 2 digit bulan
        $tahun = $date->format('y'); // 2 digit tahun

        // Get or create nomor terakhir for PUJ module
        $nomorTerakhir = NomorTerakhir::where('modul', 'PUJ')->lockForUpdate()->first();

        if (!$nomorTerakhir) {
            // Create new record if not exists
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'PUJ',
                'nomor_terakhir' => 0,
                'keterangan' => 'Pranota Uang Jalan'
            ]);
        }

        // Increment nomor terakhir
        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;

        // Update nomor terakhir
        $nomorTerakhir->nomor_terakhir = $nextNumber;
        $nomorTerakhir->save();

        // Format: PUJ-MMYY-XXXXXX (contoh: PUJ-1125-000001)
        $runningNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "PUJ-{$bulan}{$tahun}-{$runningNumber}";
    }

    /**
     * Check if user has specific pranota uang jalan permission
     */
    private function hasPranotaUangJalanPermission($user, $permission)
    {
        if (!$user) return false;

        // Admin and user_admin always have access
        if (in_array($user->role, ["admin", "user_admin"])) {
            return true;
        }

        try {
            return DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", $permission)
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}