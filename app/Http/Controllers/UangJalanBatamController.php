<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBatam;
use App\Models\UangJalanBatam;
use App\Models\PricelistUangJalanBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UangJalanBatamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:uang-jalan-batam-view')->only(['index', 'show']);
        $this->middleware('permission:uang-jalan-batam-create')->only(['create', 'store', 'selectSuratJalan']);
        $this->middleware('permission:uang-jalan-batam-update')->only(['edit', 'update']);
        $this->middleware('permission:uang-jalan-batam-delete')->only(['destroy']);
    }

    /**
     * Display a listing of uang jalan batam records.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');
        $tanggal_dari = $request->get('tanggal_dari', '');
        $tanggal_sampai = $request->get('tanggal_sampai', '');

        $query = UangJalanBatam::with(['suratJalanBatam.orderBatam', 'createdBy']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_uang_jalan', 'like', "%{$search}%")
                  ->orWhere('memo', 'like', "%{$search}%")
                  ->orWhereHas('suratJalanBatam', function ($sj) use ($search) {
                      $sj->where('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%")
                        ->orWhere('no_plat', 'like', "%{$search}%");
                  });
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($tanggal_dari) {
            $query->whereDate('tanggal_uang_jalan', '>=', $tanggal_dari);
        }
        if ($tanggal_sampai) {
            $query->whereDate('tanggal_uang_jalan', '<=', $tanggal_sampai);
        }

        $uangJalans = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $statusOptions = UangJalanBatam::getStatusOptions();
        $statusOptions = ['all' => 'Semua Status'] + $statusOptions;

        return view('uang-jalan-batam.index', compact('uangJalans', 'search', 'status', 'statusOptions', 'tanggal_dari', 'tanggal_sampai'));
    }

    /**
     * Show form to select surat jalan batam
     */
    public function selectSuratJalan(Request $request)
    {
        $search = $request->get('search', '');
        $query = SuratJalanBatam::with('orderBatam');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
        }

        // Exclude which already have uang jalan
        $existingIds = UangJalanBatam::pluck('surat_jalan_batam_id')->toArray();
        $query->whereNotIn('id', $existingIds);
        
        // Only active/completed SJ
        $query->whereIn('status', ['active', 'completed', 'sudah_checkpoint']);

        $suratJalans = $query->orderBy('created_at', 'desc')->paginate(9999);
        return view('uang-jalan-batam.select-surat-jalan', compact('suratJalans', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suratJalanId = $request->get('surat_jalan_id');
        if (!$suratJalanId) {
            return redirect()->route('uang-jalan-batam.select-surat-jalan');
        }

        $suratJalan = SuratJalanBatam::with('orderBatam')->findOrFail($suratJalanId);
        
        $exists = UangJalanBatam::where('surat_jalan_batam_id', $suratJalanId)->exists();
        if ($exists) {
            return redirect()->route('uang-jalan-batam.select-surat-jalan')->with('error', 'Uang jalan sudah dibuat.');
        }

        $nomorUangJalan = UangJalanBatam::generateNomorUangJalan();
        return view('uang-jalan-batam.create', compact('suratJalan', 'nomorUangJalan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_uang_jalan' => 'required|unique:uang_jalan_batams,nomor_uang_jalan',
            'tanggal_uang_jalan' => 'required|date',
            'surat_jalan_batam_id' => 'required|exists:surat_jalan_batams,id',
            'jumlah_uang_jalan' => 'required|numeric',
            'jumlah_mel' => 'nullable|numeric',
            'jumlah_pelancar' => 'nullable|numeric',
            'jumlah_kawalan' => 'nullable|numeric',
            'jumlah_parkir' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'jumlah_penyesuaian' => 'nullable|numeric',
            'memo' => 'nullable|string',
            'status' => 'required|in:belum_dibayar,belum_masuk_pranota,sudah_masuk_pranota,lunas,dibatalkan'
        ]);

        $subtotal = $validated['jumlah_uang_jalan'] + 
                    ($validated['jumlah_mel'] ?? 0) + 
                    ($validated['jumlah_pelancar'] ?? 0) + 
                    ($validated['jumlah_kawalan'] ?? 0) + 
                    ($validated['jumlah_parkir'] ?? 0);
        
        $validated['subtotal'] = $subtotal;
        $validated['jumlah_total'] = $subtotal + ($validated['jumlah_penyesuaian'] ?? 0);
        $validated['created_by'] = Auth::id();

        UangJalanBatam::create($validated);

        // Update SJ status
        $sj = SuratJalanBatam::find($validated['surat_jalan_batam_id']);
        $sj->update(['status_pembayaran_uang_jalan' => 'sudah_ada']);

        return redirect()->route('uang-jalan-batam.index')->with('success', 'Uang jalan Batam berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $uangJalan = UangJalanBatam::with(['suratJalanBatam.orderBatam', 'createdBy'])->findOrFail($id);
        return view('uang-jalan-batam.show', compact('uangJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $uangJalan = UangJalanBatam::with(['suratJalanBatam.orderBatam'])->findOrFail($id);
        return view('uang-jalan-batam.edit', compact('uangJalan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $uangJalan = UangJalanBatam::findOrFail($id);
        
        $validated = $request->validate([
            'nomor_uang_jalan' => 'required|unique:uang_jalan_batams,nomor_uang_jalan,'.$id,
            'tanggal_uang_jalan' => 'required|date',
            'jumlah_uang_jalan' => 'required|numeric',
            'jumlah_mel' => 'nullable|numeric',
            'jumlah_pelancar' => 'nullable|numeric',
            'jumlah_kawalan' => 'nullable|numeric',
            'jumlah_parkir' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'jumlah_penyesuaian' => 'nullable|numeric',
            'memo' => 'nullable|string',
            'status' => 'required|in:belum_dibayar,belum_masuk_pranota,sudah_masuk_pranota,lunas,dibatalkan'
        ]);

        $subtotal = $validated['jumlah_uang_jalan'] + 
                    ($validated['jumlah_mel'] ?? 0) + 
                    ($validated['jumlah_pelancar'] ?? 0) + 
                    ($validated['jumlah_kawalan'] ?? 0) + 
                    ($validated['jumlah_parkir'] ?? 0);
        
        $validated['subtotal'] = $subtotal;
        $validated['jumlah_total'] = $subtotal + ($validated['jumlah_penyesuaian'] ?? 0);

        $uangJalan->update($validated);

        return redirect()->route('uang-jalan-batam.index')->with('success', 'Uang jalan Batam berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $uangJalan = UangJalanBatam::findOrFail($id);
        $sjId = $uangJalan->surat_jalan_batam_id;
        $uangJalan->delete();
        
        SuratJalanBatam::where('id', $sjId)->update(['status_pembayaran_uang_jalan' => 'belum_ada']);

        return redirect()->route('uang-jalan-batam.index')->with('success', 'Uang jalan Batam berhasil dihapus.');
    }
}
