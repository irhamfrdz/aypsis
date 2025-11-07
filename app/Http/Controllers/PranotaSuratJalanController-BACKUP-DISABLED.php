<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\PranotaSuratJalan;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PranotaSuratJalanController extends Controller
{
    /**
     * Display a listing of pranota surat jalan.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaSuratJalanPermission($user, 'pranota-surat-jalan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota surat jalan.');
        }

        // Build query with filters
        $query = PranotaSuratJalan::with(['suratJalans'])
            ->withCount('suratJalans');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhereHas('suratJalans', function($sq) use ($search) {
                      $sq->where('nomor_surat_jalan', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter - menggunakan status_pembayaran
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Get pranota surat jalan with pagination
        $pranotaSuratJalans = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Get statistics - menggunakan status_pembayaran
        $stats = [
            'total' => PranotaSuratJalan::count(),
            'this_month' => PranotaSuratJalan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'unpaid' => PranotaSuratJalan::where('status_pembayaran', 'unpaid')->count(),
            'paid' => PranotaSuratJalan::where('status_pembayaran', 'paid')->count(),
        ];

        return view('pranota-surat-jalan.index', compact('pranotaSuratJalans', 'stats'));
    }

    /**
     * Show the form for creating a new pranota surat jalan.
     */
    public function create()
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaSuratJalanPermission($user, 'pranota-surat-jalan-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota surat jalan.');
        }

        // Get surat jalan yang belum ada pranota
        // Removed approval and checkpoint requirements - semua surat jalan bisa dimasukkan ke pranota
        $approvedSuratJalans = SuratJalan::whereDoesntHave('pranotaSuratJalan')
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->get();

        return view('pranota-surat-jalan.create', compact('approvedSuratJalans'));
    }

    /**
     * Store a newly created pranota surat jalan in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaSuratJalanPermission($user, 'pranota-surat-jalan-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota surat jalan.');
        }

        // Validation
        $request->validate([
            'surat_jalan_ids' => 'required|array|min:1',
            'surat_jalan_ids.*' => 'exists:surat_jalans,id',
            'tanggal_pranota' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor pranota
            $nomorPranota = $this->generateNomorPranota();
            $date = Carbon::now();

            // Calculate total uang jalan from selected surat jalans
            $totalUangJalan = 0;
            $suratJalans = SuratJalan::whereIn('id', $request->surat_jalan_ids)->get();

            foreach ($suratJalans as $suratJalan) {
                $totalUangJalan += $suratJalan->uang_jalan ?? 0;
            }

            // Create pranota surat jalan
            $pranotaSuratJalan = PranotaSuratJalan::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_tagihan' => $date->format('Y-m'),
                'jumlah_surat_jalan' => count($request->surat_jalan_ids),
                'total_amount' => $totalUangJalan,
                'status_pembayaran' => 'unpaid',
                'catatan' => $request->keterangan,
                'created_by' => $user->id,
            ]);

            // Attach surat jalans to pranota
            $pranotaSuratJalan->suratJalans()->attach($request->surat_jalan_ids);

            Log::info('Pranota surat jalan created', [
                'pranota_id' => $pranotaSuratJalan->id,
                'nomor_pranota' => $nomorPranota,
                'surat_jalan_count' => count($request->surat_jalan_ids),
                'total_uang_jalan' => $totalUangJalan,
                'created_by' => $user->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-surat-jalan.index')
                ->with('success', 'Pranota surat jalan berhasil dibuat dengan nomor: ' . $nomorPranota);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pranota surat jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pranota surat jalan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified pranota surat jalan.
     */
    public function show(PranotaSuratJalan $pranotaSuratJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaSuratJalanPermission($user, 'pranota-surat-jalan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota surat jalan.');
        }

        // Load relationships
        $pranotaSuratJalan->load(['suratJalans', 'creator']);

        return view('pranota-surat-jalan.show', compact('pranotaSuratJalan'));
    }

    /**
     * Show the form for editing the specified pranota surat jalan.
     */
    public function edit(PranotaSuratJalan $pranotaSuratJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaSuratJalanPermission($user, 'pranota-surat-jalan-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota surat jalan.');
        }

        // Only allow editing if status is unpaid
        if ($pranotaSuratJalan->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat diubah.');
        }

        $pranotaSuratJalan->load(['suratJalans']);

        return view('pranota-surat-jalan.edit', compact('pranotaSuratJalan'));
    }

    /**
     * Update the specified pranota surat jalan in storage.
     */
    public function update(Request $request, PranotaSuratJalan $pranotaSuratJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaSuratJalanPermission($user, 'pranota-surat-jalan-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota surat jalan.');
        }

        // Only allow updating if status is unpaid
        if ($pranotaSuratJalan->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat diubah.');
        }

        // Validation
        $request->validate([
            'tanggal_pranota' => 'required|date',
            'periode_tagihan' => 'required|string|max:20',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            $pranotaSuratJalan->update([
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_tagihan' => $request->periode_tagihan,
                'catatan' => $request->catatan,
                'updated_by' => $user->id,
            ]);

            Log::info('Pranota surat jalan updated', [
                'pranota_id' => $pranotaSuratJalan->id,
                'updated_by' => $user->name,
            ]);

            return redirect()->route('pranota-surat-jalan.show', $pranotaSuratJalan)
                ->with('success', 'Pranota surat jalan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error updating pranota surat jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui pranota surat jalan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified pranota surat jalan from storage.
     */
    public function destroy(PranotaSuratJalan $pranotaSuratJalan)
    {
        $user = Auth::user();

        // Check permission
        if (!$this->hasPranotaSuratJalanPermission($user, 'pranota-surat-jalan-delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pranota surat jalan.');
        }

        // Only allow deleting if status is unpaid
        if ($pranotaSuratJalan->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Restore surat jalan status
            $pranotaSuratJalan->suratJalans()->update(['status_pembayaran' => 'belum_bayar']);

            // Detach surat jalans
            $pranotaSuratJalan->suratJalans()->detach();

            // Delete pranota
            $pranotaSuratJalan->delete();

            Log::info('Pranota surat jalan deleted', [
                'pranota_id' => $pranotaSuratJalan->id,
                'deleted_by' => $user->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-surat-jalan.index')
                ->with('success', 'Pranota surat jalan berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pranota surat jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pranota surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor pranota dengan format PSJ-MMYY-XXXXXX
     */
    private function generateNomorPranota()
    {
        $date = Carbon::now();
        $bulan = $date->format('m'); // 2 digit bulan
        $tahun = $date->format('y'); // 2 digit tahun

        // Get or create nomor terakhir for PSJ module
        $nomorTerakhir = NomorTerakhir::where('modul', 'PSJ')->lockForUpdate()->first();

        if (!$nomorTerakhir) {
            // Create new record if not exists
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'PSJ',
                'nomor_terakhir' => 0,
                'keterangan' => 'Pranota Surat Jalan'
            ]);
        }

        // Increment nomor terakhir
        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;

        // Update nomor terakhir
        $nomorTerakhir->nomor_terakhir = $nextNumber;
        $nomorTerakhir->save();

        // Format: PSJ-MMYY-XXXXXX (contoh: PSJ-1025-000001)
        $runningNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "PSJ-{$bulan}{$tahun}-{$runningNumber}";
    }

    /**
     * Calculate tarif for surat jalan
     */
    private function calculateTarif(SuratJalan $suratJalan)
    {
        // Placeholder calculation - sesuaikan dengan business logic
        $baseTarif = 100000; // Base tarif

        // Add multiplier based on container size
        if ($suratJalan->size == '40') {
            $baseTarif *= 1.5;
        }

        // Add multiplier based on activity type
        $kegiatanMultiplier = 1;
        switch (strtolower($suratJalan->kegiatan)) {
            case 'pengiriman':
                $kegiatanMultiplier = 1.2;
                break;
            case 'pengambilan':
                $kegiatanMultiplier = 1.1;
                break;
            case 'antar isi':
                $kegiatanMultiplier = 1.3;
                break;
            default:
                $kegiatanMultiplier = 1;
        }

        return $baseTarif * $kegiatanMultiplier * $suratJalan->jumlah_kontainer;
    }

    /**
     * Check if user has specific pranota surat jalan permission
     */
    private function hasPranotaSuratJalanPermission($user, $permission)
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
