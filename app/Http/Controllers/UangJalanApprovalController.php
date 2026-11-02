<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\UangJalanApprovalRequest;
use App\Services\UangJalanApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UangJalanApprovalController extends Controller
{
    public function index(Request $request, UangJalanApprovalService $service): View
    {
        $filters = $request->validate(['status' => 'nullable|in:pending,approved,rejected,all']);
        $status = $filters['status'] ?? 'pending';
        $pengajuan = UangJalanApprovalRequest::with(['requester', 'reviewer', 'uangJalan'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();
        $tugasBerjalan = $pengajuan->getCollection()->mapWithKeys(fn ($item) => [
            $item->id => $item->status === 'pending' ? $service->tugasBerjalan($item->supir) : collect(),
        ]);

        return view('uang-jalan.approval.index', compact('pengajuan', 'status', 'tugasBerjalan'));
    }

    public function approve(Request $request, UangJalanApprovalRequest $pengajuan, UangJalanApprovalService $service): RedirectResponse
    {
        $data = $request->validate(['catatan_approval' => 'nullable|string|max:1000']);

        DB::transaction(function () use ($pengajuan, $service, $data, $request) {
            $pengajuan = UangJalanApprovalRequest::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            if ($pengajuan->status !== 'pending') {
                throw ValidationException::withMessages(['pengajuan' => 'Pengajuan ini sudah diproses.']);
            }

            $model = $pengajuan->jenis_surat_jalan === 'bongkaran' ? SuratJalanBongkaran::class : SuratJalan::class;
            $suratJalan = $model::whereKey($pengajuan->surat_jalan_id)->lockForUpdate()->firstOrFail();
            if ($suratJalan->supir !== $pengajuan->supir || ($pengajuan->jenis_surat_jalan === 'biasa' && ($suratJalan->is_supir_customer || in_array($suratJalan->status, ['cancelled', 'draft'])))) {
                throw ValidationException::withMessages(['pengajuan' => 'Data supir pada surat jalan berubah. Tolak pengajuan ini dan minta pengajuan baru.']);
            }

            $uangJalan = $service->buatUangJalan($suratJalan, $pengajuan->jenis_surat_jalan, $pengajuan->data_uang_jalan, $pengajuan->requested_by, true);
            $pengajuan->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
                'catatan_approval' => $data['catatan_approval'] ?? null,
                'uang_jalan_id' => $uangJalan->id,
            ]);
        });

        return back()->with('success', 'Pengajuan disetujui dan uang jalan berhasil dibuat.');
    }

    public function reject(Request $request, UangJalanApprovalRequest $pengajuan): RedirectResponse
    {
        $data = $request->validate(['catatan_approval' => 'required|string|max:1000']);

        DB::transaction(function () use ($pengajuan, $data, $request) {
            $pengajuan = UangJalanApprovalRequest::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            if ($pengajuan->status !== 'pending') {
                throw ValidationException::withMessages(['pengajuan' => 'Pengajuan ini sudah diproses.']);
            }
            $pengajuan->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
                'catatan_approval' => $data['catatan_approval'],
            ]);
        });

        return back()->with('success', 'Pengajuan uang jalan ditolak.');
    }
}
