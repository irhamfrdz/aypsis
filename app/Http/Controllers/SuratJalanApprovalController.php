<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanApproval;
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
            
            Log::info('Surat jalan approved', [
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => $approvalLevel,
                'approved_by' => $user->name,
            ]);

            DB::commit();

            return redirect()->route('approval.surat-jalan.index')
                ->with('success', 'Surat jalan berhasil di-approve!');

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
