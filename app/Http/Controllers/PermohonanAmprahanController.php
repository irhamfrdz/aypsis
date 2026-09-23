<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\Mobil;
use App\Models\PermohonanAmprahan;
use App\Models\PermohonanAmprahanItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanAmprahanController extends Controller
{
    public function create()
    {
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();

        return view('permohonan-amprahan.create', compact('kapals', 'mobils'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_amprahan' => 'required|in:kapal,kendaraan',
            'kapal_id' => 'nullable|required_if:jenis_amprahan,kapal|exists:master_kapals,id',
            'mobil_id' => 'nullable|required_if:jenis_amprahan,kendaraan|exists:mobils,id',
            'nomor_voyage' => 'nullable|string|max:255',
            'keterangan_umum' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.link_barang' => 'nullable|url|max:2048',
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.keterangan' => 'nullable|string',
        ], [
            'kapal_id.required_if' => 'Kapal wajib dipilih untuk jenis amprahan kapal.',
            'mobil_id.required_if' => 'Kendaraan wajib dipilih untuk jenis amprahan kendaraan.',
            'items.required' => 'Minimal satu barang harus ditambahkan.',
        ]);

        DB::transaction(function () use ($validated) {
            $permohonan = PermohonanAmprahan::create([
                'user_id' => Auth::id(),
                'tanggal_permohonan' => now(),
                'jenis_amprahan' => $validated['jenis_amprahan'],
                'kapal_id' => $validated['jenis_amprahan'] === 'kapal' ? ($validated['kapal_id'] ?? null) : null,
                'mobil_id' => $validated['jenis_amprahan'] === 'kendaraan' ? ($validated['mobil_id'] ?? null) : null,
                'nomor_voyage' => $validated['nomor_voyage'] ?? null,
                'status' => 'pending',
                'keterangan_umum' => $validated['keterangan_umum'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $permohonan->items()->create($item);
            }
        });

        return redirect()->route('permohonan-amprahan.index')
            ->with('success', 'Permintaan amprahan berhasil dibuat.');
    }

    public function index(Request $request)
    {
        $kapals = MasterKapal::orderBy('nama_kapal')->get();
        $selectedKapal = $request->input('kapal_id');
        $selectedVoyage = $request->input('nomor_voyage');

        $query = PermohonanAmprahan::with(['kapal', 'mobil', 'user', 'items'])->latest();

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
        $permohonan = PermohonanAmprahan::with(['kapal', 'mobil', 'user', 'items'])->findOrFail($id);
        
        return view('permohonan-amprahan.show', compact('permohonan'));
    }

    public function print($id)
    {
        $permohonan = PermohonanAmprahan::with(['kapal', 'mobil', 'user', 'items'])->findOrFail($id);
        
        // Since AYPSIS usually uses DOMPDF for printing, or just a printable view.
        // I will just return a view with window.print()
        return view('permohonan-amprahan.print', compact('permohonan'));
    }

    public function approvalIndex(Request $request)
    {
        $selectedStatus = $request->input('status', 'pending');

        $query = PermohonanAmprahan::with(['kapal', 'mobil', 'user', 'items'])->latest();
        
        if ($selectedStatus && $selectedStatus != 'all') {
            $query->where('status', $selectedStatus);
        }

        $permohonans = $query->paginate(15)->withQueryString();

        return view('permohonan-amprahan.approval-index', compact('permohonans', 'selectedStatus'));
    }

    public function approvalProcessForm($id)
    {
        $permohonan = PermohonanAmprahan::with(['items', 'kapal', 'mobil', 'user'])->findOrFail($id);
        
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
