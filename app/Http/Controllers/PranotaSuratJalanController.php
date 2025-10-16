<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\PranotaSuratJalan;
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
    public function index()
    {
        $user = Auth::user();
        
        // Check permission
        if (!$user->can('pranota-surat-jalan-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pranota surat jalan.');
        }

        // Get pranota surat jalan with pagination
        $pranotaSuratJalans = PranotaSuratJalan::with(['suratJalan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get statistics
        $stats = [
            'total' => PranotaSuratJalan::count(),
            'this_month' => PranotaSuratJalan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'pending' => PranotaSuratJalan::where('status', 'pending')->count(),
            'paid' => PranotaSuratJalan::where('status', 'paid')->count(),
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
        if (!$user->can('pranota-surat-jalan-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pranota surat jalan.');
        }

        // Get surat jalan yang sudah fully approved tapi belum ada pranota
        $approvedSuratJalans = SuratJalan::whereHas('approvals', function($query) {
                $query->where('approval_level', 'tugas-1')->where('status', 'approved');
            })
            ->whereHas('approvals', function($query) {
                $query->where('approval_level', 'tugas-2')->where('status', 'approved');
            })
            ->whereDoesntHave('pranotaSuratJalan')
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
        if (!$user->can('pranota-surat-jalan-create')) {
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

            // Calculate total tarif from selected surat jalans
            $totalTarif = 0;
            $suratJalans = SuratJalan::whereIn('id', $request->surat_jalan_ids)->get();
            
            foreach ($suratJalans as $suratJalan) {
                $totalTarif += $suratJalan->tarif ?? 0;
            }

            // Create pranota surat jalan
            $pranotaSuratJalan = PranotaSuratJalan::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'total_tarif' => $totalTarif,
                'status' => 'draft',
                'keterangan' => $request->keterangan,
                'user_id' => $user->id,
            ]);

            // Attach surat jalans to pranota
            $pranotaSuratJalan->suratJalans()->attach($request->surat_jalan_ids);

            Log::info('Pranota surat jalan created', [
                'pranota_id' => $pranotaSuratJalan->id,
                'nomor_pranota' => $nomorPranota,
                'surat_jalan_count' => count($request->surat_jalan_ids),
                'total_tarif' => $totalTarif,
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
        if (!$user->can('pranota-surat-jalan-view')) {
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
        if (!$user->can('pranota-surat-jalan-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota surat jalan.');
        }

        // Only allow editing if status is pending
        if ($pranotaSuratJalan->status !== 'pending') {
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
        if (!$user->can('pranota-surat-jalan-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pranota surat jalan.');
        }

        // Only allow updating if status is pending
        if ($pranotaSuratJalan->status !== 'pending') {
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
        if (!$user->can('pranota-surat-jalan-delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pranota surat jalan.');
        }

        // Only allow deleting if status is pending
        if ($pranotaSuratJalan->status !== 'pending') {
            return back()->with('error', 'Pranota yang sudah diproses tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            // Restore surat jalan status
            $pranotaSuratJalan->suratJalans()->update(['status' => 'fully_approved']);

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
     * Generate nomor pranota
     */
    private function generateNomorPranota()
    {
        $date = Carbon::now();
        $prefix = 'PSJ/' . $date->format('Y/m') . '/';
        
        // Get the last number for this month
        $lastPranota = PranotaSuratJalan::where('nomor_pranota', 'like', $prefix . '%')
            ->orderBy('nomor_pranota', 'desc')
            ->first();

        if ($lastPranota) {
            $lastNumber = intval(substr($lastPranota->nomor_pranota, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
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
}