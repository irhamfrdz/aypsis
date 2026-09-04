<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\PermohonanAmprahan;
use App\Models\PermohonanAmprahanItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanAmprahanController extends Controller
{
    public function index(Request $request)
    {
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $selectedKapal = $request->input('kapal_id');
        $selectedVoyage = $request->input('nomor_voyage');

        $query = PermohonanAmprahan::with(['kapal', 'user'])->latest();

        if ($selectedKapal) {
            $query->where('kapal_id', $selectedKapal);
        }

        if ($selectedVoyage) {
            $query->where('nomor_voyage', 'like', "%{$selectedVoyage}%");
        }

        // if neither is selected, maybe return empty? The user said "halaman pertamanya adalah halaman untuk pilih kapal dan voyage -> muncul semua permohonan yang sudah diinput"
        // We'll show list only if at least one filter is applied, or we can just paginate all.
        // Let's paginate all but they are filterable.
        $permohonans = $query->paginate(15)->withQueryString();

        return view('permohonan-amprahan.index', compact('kapals', 'permohonans', 'selectedKapal', 'selectedVoyage'));
    }

    public function show($id)
    {
        $permohonan = PermohonanAmprahan::with(['kapal', 'user', 'items'])->findOrFail($id);
        
        return view('permohonan-amprahan.show', compact('permohonan'));
    }

    public function print($id)
    {
        $permohonan = PermohonanAmprahan::with(['kapal', 'user', 'items'])->findOrFail($id);
        
        // Since AYPSIS usually uses DOMPDF for printing, or just a printable view.
        // I will just return a view with window.print()
        return view('permohonan-amprahan.print', compact('permohonan'));
    }

    public function approvalIndex(Request $request)
    {
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $selectedKapal = $request->input('kapal_id');
        $selectedVoyage = $request->input('nomor_voyage');
        $selectedStatus = $request->input('status', 'pending');

        $query = PermohonanAmprahan::with(['kapal', 'user'])->latest();

        if ($selectedKapal) {
            $query->where('kapal_id', $selectedKapal);
        }

        if ($selectedVoyage) {
            $query->where('nomor_voyage', 'like', "%{$selectedVoyage}%");
        }
        
        if ($selectedStatus && $selectedStatus != 'all') {
            $query->where('status', $selectedStatus);
        }

        $permohonans = $query->paginate(15)->withQueryString();

        return view('permohonan-amprahan.approval-index', compact('kapals', 'permohonans', 'selectedKapal', 'selectedVoyage', 'selectedStatus'));
    }

    public function approvalProcessForm($id)
    {
        $permohonan = PermohonanAmprahan::with(['items', 'kapal', 'user'])->findOrFail($id);
        
        return view('permohonan-amprahan.approval-process', compact('permohonan'));
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|in:approved,rejected,pending',
        ]);

        $permohonan = PermohonanAmprahan::findOrFail($id);
        
        // Update item statuses
        $approvedCount = 0;
        $rejectedCount = 0;
        $totalItems = count($request->items);
        
        foreach ($request->items as $itemId => $status) {
            $item = PermohonanAmprahanItem::where('permohonan_id', $id)->where('id', $itemId)->first();
            if ($item) {
                $item->status = $status;
                $item->save();
                
                if ($status == 'approved') $approvedCount++;
                if ($status == 'rejected') $rejectedCount++;
            }
        }

        // Determine parent status
        if ($approvedCount == $totalItems) {
            $permohonan->status = 'approved';
        } elseif ($rejectedCount == $totalItems) {
            $permohonan->status = 'rejected';
        } else {
            $permohonan->status = 'partially_approved';
        }
        
        $permohonan->save();

        return redirect()->route('approval-permohonan-amprahan.index')
                         ->with('success', 'Persetujuan permohonan amprahan berhasil diproses.');
    }
}
