<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanApproval;
use App\Models\TandaTerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuratJalanApprovalController extends Controller
{
    /**
     * Dashboard untuk melihat surat jalan yang perlu approval
     */
    public function index()
    {
        $user = Auth::user();

        // Approval surat jalan hanya 1 level
        $approvalLevel = 'approval';

        // Ambil surat jalan yang perlu approval
        $pendingApprovals = SuratJalanApproval::with(['suratJalan', 'approver'])
            ->where('approval_level', $approvalLevel)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Ambil statistik
        $stats = [
            'pending' => SuratJalanApproval::where('approval_level', $approvalLevel)
                ->where('status', 'pending')->count(),
            'approved_today' => SuratJalanApproval::where('approval_level', $approvalLevel)
                ->where('status', 'approved')
                ->whereDate('approved_at', now())->count(),
            'approved_total' => SuratJalanApproval::where('approval_level', $approvalLevel)
                ->where('status', 'approved')->count(),
        ];

        return view('approval.surat-jalan.index', compact('pendingApprovals', 'approvalLevel', 'stats'));
    }

    /**
     * Detail surat jalan untuk approval
     */
    public function show(SuratJalan $suratJalan)
    {
        $user = Auth::user();

        // Approval surat jalan hanya 1 level
        $approvalLevel = 'approval';

        // Ambil approval record
        $approval = SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)
            ->where('approval_level', $approvalLevel)
            ->first();

        if (!$approval) {
            abort(404, 'Approval record tidak ditemukan.');
        }

        // Load relationships
        $suratJalan->load(['approvals.approver']);

        return view('approval.surat-jalan.show', compact('suratJalan', 'approval', 'approvalLevel'));
    }

    /**
     * Approve surat jalan
     */
    public function approve(Request $request, SuratJalan $suratJalan)
    {
        $user = Auth::user();

        // Approval surat jalan hanya 1 level
        $approvalLevel = 'approval';

        // Validasi input
        $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Ambil approval record
            $approval = SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)
                ->where('approval_level', $approvalLevel)
                ->where('status', 'pending')
                ->first();

            if (!$approval) {
                throw new \Exception('Approval record tidak ditemukan atau sudah diproses.');
            }

            // Update approval record
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approval_notes' => $request->approval_notes,
                'approved_at' => now(),
            ]);

            // Update status surat jalan menjadi approved
            $suratJalan->update(['status' => 'approved']);

            // Update status kontainer berdasarkan kegiatan
            if ($suratJalan->no_kontainer) {
                $this->updateKontainerStatus($suratJalan);
            }

            // Create tanda terima automatically
            $this->createTandaTerima($suratJalan);

            Log::info('Surat jalan approved', [
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => $approvalLevel,
                'approved_by' => $user->name,
                'kontainer_updated' => $suratJalan->no_kontainer ? 'yes' : 'no',
                'tanda_terima_created' => 'yes',
            ]);

            DB::commit();

            return redirect()->route('approval.surat-jalan.index')
                ->with('success', 'Surat jalan berhasil di-approve! Status kontainer telah diperbarui dan tanda terima telah dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving surat jalan: ' . $e->getMessage(), [
                'surat_jalan_id' => $suratJalan->id,
                'user_id' => $user->id,
            ]);
            return back()->with('error', 'Gagal approve surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Create tanda terima from approved surat jalan
     */
    private function createTandaTerima(SuratJalan $suratJalan)
    {
        // Check if tanda terima already exists
        $existingTandaTerima = TandaTerima::where('surat_jalan_id', $suratJalan->id)->first();

        if ($existingTandaTerima) {
            Log::info('Tanda terima already exists', ['surat_jalan_id' => $suratJalan->id]);
            return;
        }

        // Create new tanda terima with data from surat jalan
        TandaTerima::create([
            'surat_jalan_id' => $suratJalan->id,
            'no_surat_jalan' => $suratJalan->no_surat_jalan,
            'tanggal_surat_jalan' => $suratJalan->tanggal_surat_jalan,
            'supir' => $suratJalan->supir,
            'kegiatan' => $suratJalan->kegiatan,
            'jenis_barang' => $suratJalan->jenis_barang,
            'size' => $suratJalan->size,
            'jumlah_kontainer' => $suratJalan->jumlah_kontainer,
            'no_kontainer' => $suratJalan->no_kontainer,
            'no_seal' => $suratJalan->no_seal,
            'tujuan_pengiriman' => $suratJalan->tujuan_pengiriman,
            'pengirim' => $suratJalan->pengirim,
            'gambar_checkpoint' => $suratJalan->gambar_checkpoint,
            'status' => 'draft', // Default status
            'created_by' => Auth::id(),
        ]);

        Log::info('Tanda terima created from surat jalan', [
            'surat_jalan_id' => $suratJalan->id,
            'no_surat_jalan' => $suratJalan->no_surat_jalan,
        ]);
    }

    /**
     * Update status kontainer berdasarkan kegiatan surat jalan
     */
    private function updateKontainerStatus(SuratJalan $suratJalan)
    {
        // Parse nomor kontainer (bisa lebih dari 1, dipisah koma)
        $nomorKontainers = array_map('trim', explode(',', $suratJalan->no_kontainer));

        foreach ($nomorKontainers as $nomorKontainer) {
            if (empty($nomorKontainer)) continue;

            // Tentukan status baru berdasarkan kegiatan
            $statusBaru = $this->determineKontainerStatus($suratJalan->kegiatan);

            // Update di tabel kontainers
            \App\Models\Kontainer::where('nomor_seri_gabungan', $nomorKontainer)
                ->update([
                    'status' => $statusBaru,
                    'updated_at' => now(),
                ]);

            // Update di tabel stock_kontainers
            \App\Models\StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)
                ->update([
                    'status' => $statusBaru,
                    'tanggal_keluar' => in_array($suratJalan->kegiatan, ['stuffing', 'antar']) ? now() : null,
                    'updated_at' => now(),
                ]);

            Log::info('Kontainer status updated', [
                'nomor_kontainer' => $nomorKontainer,
                'kegiatan' => $suratJalan->kegiatan,
                'status_baru' => $statusBaru,
                'surat_jalan_id' => $suratJalan->id,
            ]);
        }
    }

    /**
     * Tentukan status kontainer berdasarkan kegiatan
     */
    private function determineKontainerStatus($kegiatan)
    {
        $statusMap = [
            'stuffing' => 'terisi',           // Kontainer sudah diisi barang
            'antar' => 'terkirim',            // Kontainer sudah diantar ke tujuan
            'jemput' => 'active',             // Kontainer dijemput (kembali aktif)
            'kosong' => 'kosong',             // Kontainer kosong
            'perbaikan' => 'perbaikan',       // Kontainer dalam perbaikan
            'cuci' => 'cuci',                 // Kontainer sedang dicuci
        ];

        return $statusMap[$kegiatan] ?? 'active';
    }

    /**
     * Reject surat jalan
     */
    public function reject(Request $request, SuratJalan $suratJalan)
    {
        $user = Auth::user();

        // Approval surat jalan hanya 1 level
        $approvalLevel = 'approval';

        // Validasi input
        $request->validate([
            'approval_notes' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Ambil approval record
            $approval = SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)
                ->where('approval_level', $approvalLevel)
                ->where('status', 'pending')
                ->first();

            if (!$approval) {
                throw new \Exception('Approval record tidak ditemukan atau sudah diproses.');
            }

            // Update approval record
            $approval->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approval_notes' => $request->approval_notes,
                'approved_at' => now(),
            ]);

            // Update status surat jalan menjadi rejected
            $suratJalan->update(['status' => 'rejected']);

            Log::info('Surat jalan rejected', [
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => $approvalLevel,
                'rejected_by' => $user->name,
                'reason' => $request->approval_notes,
            ]);

            DB::commit();
            return redirect()->route('approval.surat-jalan.index')
                ->with('success', 'Surat jalan berhasil di-reject.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting surat jalan: ' . $e->getMessage());
            return back()->with('error', 'Gagal reject surat jalan: ' . $e->getMessage());
        }
    }
}
