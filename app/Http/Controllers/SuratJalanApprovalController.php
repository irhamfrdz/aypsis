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
        
        // Tentukan level approval berdasarkan permission user
        $approvalLevel = null;
        if ($user->can('approval-tugas-1.view')) {
            $approvalLevel = 'tugas-1';
        } elseif ($user->can('approval-tugas-2.view')) {
            $approvalLevel = 'tugas-2';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk melihat approval surat jalan.');
        }

        // Ambil surat jalan yang perlu approval untuk level ini
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
        
        // Tentukan level approval berdasarkan permission user
        $approvalLevel = null;
        if ($user->can('approval-tugas-1.view')) {
            $approvalLevel = 'tugas-1';
        } elseif ($user->can('approval-tugas-2.view')) {
            $approvalLevel = 'tugas-2';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk melihat approval surat jalan.');
        }

        // Ambil approval record untuk level ini
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
        
        // Tentukan level approval dan permission yang dibutuhkan
        $approvalLevel = null;
        $requiredPermission = null;
        
        if ($user->can('approval-tugas-1.approve')) {
            $approvalLevel = 'tugas-1';
            $requiredPermission = 'approval-tugas-1.approve';
        } elseif ($user->can('approval-tugas-2.approve')) {
            $approvalLevel = 'tugas-2';
            $requiredPermission = 'approval-tugas-2.approve';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk approve surat jalan.');
        }

        // Validasi input
        $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Ambil approval record untuk level ini
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

            // Check apakah semua approval level sudah selesai
            $allApprovals = SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)->get();
            $allApproved = $allApprovals->every(function ($item) {
                return $item->status === 'approved';
            });

            // Jika semua sudah approved, update status surat jalan
            if ($allApproved) {
                $suratJalan->update(['status' => 'fully_approved']);
                Log::info('Surat jalan fully approved', [
                    'surat_jalan_id' => $suratJalan->id,
                    'final_approver' => $user->name,
                ]);
            }

            Log::info('Surat jalan approved', [
                'surat_jalan_id' => $suratJalan->id,
                'approval_level' => $approvalLevel,
                'approved_by' => $user->name,
                'all_approved' => $allApproved,
            ]);

            DB::commit();
            
            $message = $allApproved ? 
                'Surat jalan berhasil di-approve. Semua tahap approval telah selesai!' : 
                'Surat jalan berhasil di-approve untuk level ' . $approvalLevel . '.';
                
            return redirect()->route('approval.surat-jalan.index')->with('success', $message);
            
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
        
        // Tentukan level approval
        $approvalLevel = null;
        if ($user->can('approval-tugas-1.approve')) {
            $approvalLevel = 'tugas-1';
        } elseif ($user->can('approval-tugas-2.approve')) {
            $approvalLevel = 'tugas-2';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk reject surat jalan.');
        }

        // Validasi input
        $request->validate([
            'approval_notes' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Ambil approval record untuk level ini
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