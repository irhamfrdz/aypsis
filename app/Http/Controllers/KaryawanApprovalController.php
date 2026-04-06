<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\KaryawanApprovalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KaryawanApprovalController extends Controller
{
    public function index()
    {
        $requests = KaryawanApprovalRequest::with(['karyawan', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('master-karyawan.approval.index', compact('requests'));
    }

    public function approve(KaryawanApprovalRequest $approval)
    {
        if ($approval->status !== 'pending') {
            return redirect()->route('master.karyawan.approval.index')->with('error', 'Status pengajuan sudah bukan pending lagi.');
        }

        $karyawan = $approval->karyawan;
        $dataAfter = $approval->data_after;
        $validated = $dataAfter['validated'];
        $familyMembers = $dataAfter['family_members'] ?? [];

        DB::transaction(function () use ($karyawan, $validated, $familyMembers, $approval) {
            $karyawan->update($validated);

            // Handle family members update (copied from KaryawanController)
            if (isset($familyMembers)) {
                $existingIds = collect($familyMembers)->pluck('id')->filter()->toArray();
                $karyawan->familyMembers()->whereNotIn('id', $existingIds)->delete();

                foreach ($familyMembers as $memberData) {
                    if (!empty($memberData['hubungan']) && !empty($memberData['nama'])) {
                        foreach ($memberData as $key => $value) {
                            if ($value !== null && $key !== 'tanggal_lahir' && $key !== 'id') {
                                $memberData[$key] = strtoupper($value);
                            }
                        }

                        if (!empty($memberData['id'])) {
                            $familyMember = $karyawan->familyMembers()->find($memberData['id']);
                            if ($familyMember) {
                                unset($memberData['id']);
                                $familyMember->update($memberData);
                            }
                        } else {
                            unset($memberData['id']);
                            $karyawan->familyMembers()->create($memberData);
                        }
                    }
                }
            }

            $approval->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);
        });

        return redirect()->route('master.karyawan.approval.index')->with('success', 'Perubahan data karyawan berhasil disetujui.');
    }

    public function reject(Request $request, KaryawanApprovalRequest $approval)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $approval->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'reason' => $request->reason
        ]);

        return redirect()->route('master.karyawan.approval.index')->with('success', 'Perubahan data karyawan berhasil ditolak.');
    }
}
