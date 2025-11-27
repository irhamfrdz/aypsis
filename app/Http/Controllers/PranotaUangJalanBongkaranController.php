<?php

namespace App\Http\Controllers;

use App\Models\UangJalanBongkaran;
use App\Models\PranotaUangJalanBongkaran;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PranotaUangJalanBongkaranController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Permission check - reuse existing permission key
        if (!$user->can('pranota-uang-jalan-bongkaran-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota uang jalan bongkaran.');
        }

        $query = PranotaUangJalanBongkaran::with(['uangJalanBongkarans'])
            ->withCount('uangJalanBongkarans');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhereHas('uangJalanBongkarans', function($sq) use ($search) {
                      $sq->where('nomor_uang_jalan', 'like', "%{$search}%")
                         ->orWhere('no_kontainer', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter - menggunakan status_pembayaran
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Get pranota uang jalan bongkaran with pagination
        $pranotaUangJalanBongkarans = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Get statistics - menggunakan status_pembayaran
        $stats = [
            'total' => PranotaUangJalanBongkaran::count(),
            'this_month' => PranotaUangJalanBongkaran::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'unpaid' => PranotaUangJalanBongkaran::where('status_pembayaran', 'unpaid')->count(),
            'paid' => PranotaUangJalanBongkaran::where('status_pembayaran', 'paid')->count(),
        ];

        return view('pranota-uang-jalan-bongkaran.index', compact('pranotaUangJalanBongkarans', 'stats'));
    }

    /**
     * Show the form for creating a new pranota uang jalan bongkaran.
     */
    public function create()
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('pranota-uang-jalan-bongkaran-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota uang jalan bongkaran.');
        }

        // Get uang jalan bongkaran yang belum ada pranota
        $availableUangJalanBongkarans = UangJalanBongkaran::with(['suratJalanBongkaran'])
            ->whereDoesntHave('pranotaUangJalanBongkaran')
            ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
            ->orderBy('tanggal_uang_jalan', 'desc')
            ->get();

        return view('pranota-uang-jalan-bongkaran.create', compact('availableUangJalanBongkarans'));
    }

    /**
     * Store a newly created pranota uang jalan bongkaran in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('pranota-uang-jalan-bongkaran-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota uang jalan bongkaran.');
        }

        // Validation
        $request->validate([
            'uang_jalan_bongkaran_ids' => 'required|array|min:1',
            'uang_jalan_bongkaran_ids.*' => 'exists:uang_jalan_bongkarans,id',
            'tanggal_pranota' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'penyesuaian' => 'nullable|numeric',
            'penyesuaian_amount' => 'nullable|numeric|min:0',
            'keterangan_penyesuaian' => 'nullable|string|max:500',
        ]);

        // Additional validation: Ensure all selected uang jalan bongkarans are available for pranota
        $selectedUangJalanBongkarans = UangJalanBongkaran::whereIn('id', $request->uang_jalan_bongkaran_ids)
            ->whereDoesntHave('pranotaUangJalanBongkaran')
            ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
            ->get();

        if ($selectedUangJalanBongkarans->count() !== count($request->uang_jalan_bongkaran_ids)) {
            return redirect()->back()
                ->withErrors(['uang_jalan_bongkaran_ids' => 'Beberapa uang jalan bongkaran yang dipilih tidak tersedia atau sudah masuk pranota.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate nomor pranota
            $nomorPranota = $this->generateNomorPranota();
            $date = Carbon::now();

            // Calculate total from selected uang jalan bongkarans
            $totalAmount = 0;
            foreach ($selectedUangJalanBongkarans as $uangJalanBongkaran) {
                $totalAmount += $uangJalanBongkaran->jumlah_total ?? 0;
            }

            // Create pranota uang jalan bongkaran
            $pranotaUangJalanBongkaran = PranotaUangJalanBongkaran::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_tagihan' => $date->format('Y-m'),
                'jumlah_uang_jalan_bongkaran' => count($request->uang_jalan_bongkaran_ids),
                'total_amount' => $totalAmount,
                'penyesuaian' => $request->penyesuaian ?? 0,
                'keterangan_penyesuaian' => $request->keterangan_penyesuaian,
                'status_pembayaran' => 'unpaid',
                'catatan' => $request->keterangan,
                'created_by' => $user->id,
            ]);

            // Update status uang jalan bongkarans: belum_masuk_pranota -> sudah_masuk_pranota
            UangJalanBongkaran::whereIn('id', $request->uang_jalan_bongkaran_ids)
                ->update(['status' => 'sudah_masuk_pranota']);

            // Attach uang jalan bongkarans to pranota
            $pranotaUangJalanBongkaran->uangJalanBongkarans()->attach($request->uang_jalan_bongkaran_ids);

            Log::info('Pranota uang jalan bongkaran created', [
                'pranota_id' => $pranotaUangJalanBongkaran->id,
                'nomor_pranota' => $nomorPranota,
                'uang_jalan_bongkaran_count' => count($request->uang_jalan_bongkaran_ids),
                'total_amount' => $totalAmount,
                'created_by' => $user->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-jalan-bongkaran.index')
                ->with('success', 'Pranota uang jalan bongkaran berhasil dibuat dengan nomor: ' . $nomorPranota . ' dan status "Belum Dibayar". Status ' . count($request->uang_jalan_bongkaran_ids) . ' uang jalan bongkaran telah diubah menjadi "Sudah Masuk Pranota".');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pranota uang jalan bongkaran: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pranota uang jalan bongkaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified pranota uang jalan bongkaran.
     */
    public function show(PranotaUangJalanBongkaran $pranotaUangJalanBongkaran)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('pranota-uang-jalan-bongkaran-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota uang jalan bongkaran.');
        }

        // Load relationships
        $pranotaUangJalanBongkaran->load(['uangJalanBongkarans.suratJalanBongkaran', 'creator']);

        return view('pranota-uang-jalan-bongkaran.show', compact('pranotaUangJalanBongkaran'));
    }

    /**
     * Print pranota uang jalan bongkaran.
     */
    public function print(PranotaUangJalanBongkaran $pranotaUangJalanBongkaran)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('pranota-uang-jalan-bongkaran-view')) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak pranota uang jalan bongkaran.');
        }

        // Load relationships
        $pranotaUangJalanBongkaran->load(['uangJalanBongkarans.suratJalanBongkaran', 'creator']);

        return view('pranota-uang-jalan-bongkaran.print', compact('pranotaUangJalanBongkaran'));
    }

    /**
     * Show the form for editing the specified pranota uang jalan bongkaran.
     */
    public function edit(PranotaUangJalanBongkaran $pranotaUangJalanBongkaran)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('pranota-uang-jalan-bongkaran-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota uang jalan bongkaran.');
        }

        // Only allow editing if status is unpaid
        if ($pranotaUangJalanBongkaran->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat diubah.');
        }

        $pranotaUangJalanBongkaran->load(['uangJalanBongkarans']);

        return view('pranota-uang-jalan-bongkaran.edit', compact('pranotaUangJalanBongkaran'));
    }

    /**
     * Update the specified pranota uang jalan bongkaran in storage.
     */
    public function update(Request $request, PranotaUangJalanBongkaran $pranotaUangJalanBongkaran)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('pranota-uang-jalan-bongkaran-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota uang jalan bongkaran.');
        }

        // Only allow updating if status is unpaid
        if ($pranotaUangJalanBongkaran->status_pembayaran !== 'unpaid') {
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
            $pranotaUangJalanBongkaran->update([
                'tanggal_pranota' => $request->tanggal_pranota,
                'periode_tagihan' => $request->periode_tagihan,
                'catatan' => $request->catatan,
                'penyesuaian' => $request->penyesuaian ?? 0,
                'keterangan_penyesuaian' => $request->keterangan_penyesuaian,
                'updated_by' => $user->id,
            ]);

            Log::info('Pranota uang jalan bongkaran updated', [
                'pranota_id' => $pranotaUangJalanBongkaran->id,
                'updated_by' => $user->name,
            ]);

            return redirect()->route('pranota-uang-jalan-bongkaran.show', $pranotaUangJalanBongkaran)
                ->with('success', 'Pranota uang jalan bongkaran berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error updating pranota uang jalan bongkaran: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui pranota uang jalan bongkaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified pranota uang jalan bongkaran from storage.
     */
    public function destroy(PranotaUangJalanBongkaran $pranotaUangJalanBongkaran)
    {
        $user = Auth::user();

        // Check permission
        if (!$user->can('pranota-uang-jalan-bongkaran-delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pranota uang jalan bongkaran.');
        }

        // Only allow deleting if status is unpaid
        if ($pranotaUangJalanBongkaran->status_pembayaran !== 'unpaid') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Restore uang jalan bongkaran status back to 'belum_masuk_pranota' so they can be included in new pranota
            $pranotaUangJalanBongkaran->uangJalanBongkarans()->update(['status' => 'belum_masuk_pranota']);

            // Detach uang jalan bongkarans
            $pranotaUangJalanBongkaran->uangJalanBongkarans()->detach();

            // Delete pranota
            $pranotaUangJalanBongkaran->delete();

            Log::info('Pranota uang jalan bongkaran deleted', [
                'pranota_id' => $pranotaUangJalanBongkaran->id,
                'deleted_by' => $user->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-jalan-bongkaran.index')
                ->with('success', 'Pranota uang jalan bongkaran berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pranota uang jalan bongkaran: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pranota uang jalan bongkaran: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor pranota dengan format PUJB-MMYY-XXXXXX
     */
    private function generateNomorPranota()
    {
        $date = Carbon::now();
        $bulan = $date->format('m'); // 2 digit bulan
        $tahun = $date->format('y'); // 2 digit tahun

        // Get or create nomor terakhir for PUJB module
        $nomorTerakhir = NomorTerakhir::where('modul', 'PUJB')->lockForUpdate()->first();

        if (!$nomorTerakhir) {
            // Create new record if not exists
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'PUJB',
                'nomor_terakhir' => 0,
                'keterangan' => 'Pranota Uang Jalan Bongkaran'
            ]);
        }

        // Increment nomor terakhir
        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;

        // Update nomor terakhir
        $nomorTerakhir->nomor_terakhir = $nextNumber;
        $nomorTerakhir->save();

        // Format: PUJB-MMYY-XXXXXX (contoh: PUJB-1125-000001)
        $runningNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return "PUJB-{$bulan}{$tahun}-{$runningNumber}";
    }
}
