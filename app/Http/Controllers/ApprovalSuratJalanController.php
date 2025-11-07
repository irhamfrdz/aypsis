<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalanApproval;
use App\Models\SuratJalan;

class ApprovalSuratJalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:approval-surat-jalan-view')->only(['index', 'show']);
        $this->middleware('can:approval-surat-jalan-approve')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        // Surat jalan uses 'approval' level, not task-based levels
        $approvalLevel = $request->get('level', 'approval');
        
        // Get pending approvals for the specified level
        $pendingApprovals = SuratJalanApproval::with(['suratJalan.order'])
            ->where('approval_level', $approvalLevel)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'pending' => SuratJalanApproval::where('approval_level', $approvalLevel)
                ->where('status', 'pending')
                ->count(),
            'approved_today' => SuratJalanApproval::where('approval_level', $approvalLevel)
                ->where('status', 'approved')
                ->whereDate('approved_at', today())
                ->count(),
            'approved_total' => SuratJalanApproval::where('approval_level', $approvalLevel)
                ->where('status', 'approved')
                ->count()
        ];

        return view('approval.surat-jalan.index', compact('pendingApprovals', 'stats', 'approvalLevel'));
    }

    public function show(Request $request, SuratJalan $suratJalan)
    {
        // Surat jalan uses 'approval' level, not task-based levels  
        $approvalLevel = $request->get('level', 'approval');
        $suratJalan->load(['order', 'approvals.approver']);
        
        // Get approval for current level
        $approval = $suratJalan->approvals->where('approval_level', $approvalLevel)->first();
        
        return view('approval.surat-jalan.show', compact('suratJalan', 'approval', 'approvalLevel'));
    }

    public function approve(Request $request, SuratJalan $suratJalan)
    {
        // Surat jalan uses 'approval' level, not task-based levels
        $approvalLevel = $request->get('level', 'approval');
        $request->validate([
            'approval_notes' => 'nullable|string|max:500'
        ]);

        $approval = SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)
            ->where('approval_level', $approvalLevel)
            ->where('status', 'pending')
            ->first();

        if (!$approval) {
            return back()->with('error', 'Approval tidak ditemukan atau sudah diproses.');
        }

        $approval->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes
        ]);

        // Process units on related order when surat jalan is approved
        if ($suratJalan->order_id && $suratJalan->jumlah_kontainer) {
            try {
                $order = $suratJalan->order;
                if ($order) {
                    $processedUnits = (int) $suratJalan->jumlah_kontainer;
                    $note = "Surat jalan diapprove: {$suratJalan->no_surat_jalan} dengan {$processedUnits} kontainer";

                    // Process units on the order - this will update completion percentage
                    $order->processUnits($processedUnits, $note);

                    \Log::info('Order units processed after approval', [
                        'order_id' => $order->id,
                        'processed_units' => $processedUnits,
                        'remaining_sisa' => $order->sisa,
                        'completion_percentage' => $order->completion_percentage
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but don't fail the approval
                \Log::error('Error processing order units after approval: ' . $e->getMessage(), [
                    'surat_jalan_id' => $suratJalan->id,
                    'order_id' => $suratJalan->order_id
                ]);
            }
        }

        return back()->with('success', 'Surat jalan berhasil diapprove untuk level ' . $approvalLevel);
    }

    public function reject(Request $request, SuratJalan $suratJalan)
    {
        // Surat jalan uses 'approval' level, not task-based levels
        $approvalLevel = $request->get('level', 'approval');
        $request->validate([
            'approval_notes' => 'required|string|max:500'
        ]);

        $approval = SuratJalanApproval::where('surat_jalan_id', $suratJalan->id)
            ->where('approval_level', $approvalLevel)
            ->where('status', 'pending')
            ->first();

        if (!$approval) {
            return back()->with('error', 'Approval tidak ditemukan atau sudah diproses.');
        }

        $approval->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes
        ]);

        return back()->with('success', 'Surat jalan berhasil ditolak untuk level ' . $approvalLevel);
    }

    /**
     * Get available stock kontainers for dropdown
     */
    public function getStockKontainers(Request $request)
    {
        $search = $request->get('search', '');
        $stockKontainers = \App\Models\StockKontainer::where('status', 'available')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nomor_seri_gabungan', 'like', "%{$search}%")
                      ->orWhere('awalan_kontainer', 'like', "%{$search}%")
                      ->orWhere('nomor_seri_kontainer', 'like', "%{$search}%")
                      ->orWhere('akhiran_kontainer', 'like', "%{$search}%");
                });
            })
            ->select('id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->limit(20)
            ->get()
            ->map(function ($kontainer) {
                return [
                    'id' => $kontainer->id,
                    'nomor' => $kontainer->nomor_seri_gabungan ?: $kontainer->nomor_kontainer,
                    'ukuran' => $kontainer->ukuran,
                    'tipe' => $kontainer->tipe_kontainer,
                    'text' => $kontainer->nomor_seri_gabungan . ' (' . $kontainer->ukuran . ' - ' . $kontainer->tipe_kontainer . ')'
                ];
            });

        return response()->json($stockKontainers);
    }

    /**
     * Update kontainer and seal for surat jalan
     */
    public function updateKontainerSeal(Request $request, SuratJalan $suratJalan)
    {
        $request->validate([
            'no_kontainer' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255'
        ]);

        // Check if user has permission
        if (!auth()->user()->can('approval-surat-jalan-approve')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit data ini.'
            ], 403);
        }

        // Check if surat jalan is already approved
        $hasApprovedApproval = $suratJalan->approvals()
            ->where('status', 'approved')
            ->exists();

        if ($hasApprovedApproval) {
            return response()->json([
                'success' => false,
                'message' => 'Surat jalan sudah diapprove, kontainer dan seal tidak dapat diubah.'
            ], 422);
        }

        // Update surat jalan
        $suratJalan->update([
            'no_kontainer' => $request->no_kontainer,
            'no_seal' => $request->no_seal
        ]);

        // If kontainer is selected from stock, mark it as rented
        if ($request->no_kontainer && $request->stock_kontainer_id) {
            $stockKontainer = \App\Models\StockKontainer::find($request->stock_kontainer_id);
            if ($stockKontainer) {
                $stockKontainer->update(['status' => 'rented']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kontainer dan seal berhasil diperbarui.',
            'data' => [
                'no_kontainer' => $suratJalan->no_kontainer,
                'no_seal' => $suratJalan->no_seal
            ]
        ]);
    }
}