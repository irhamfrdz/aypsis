<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanBongkaran;
use App\Models\UangJalanBongkaran;
use App\Models\MasterPricelistUangJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UangJalanBongkaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:uang-jalan-bongkaran-view')->only(['index', 'show']);
        $this->middleware('permission:uang-jalan-bongkaran-create')->only(['create', 'store']);
        $this->middleware('permission:uang-jalan-bongkaran-update')->only(['edit', 'update']);
        $this->middleware('permission:uang-jalan-bongkaran-delete')->only(['destroy']);
    }

    /**
     * Display a listing of uang jalan bongkaran records.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');
        $tanggal_dari = $request->get('tanggal_dari', '');
        $tanggal_sampai = $request->get('tanggal_sampai', '');

        // Query uang jalan bongkaran dengan relasi
        $query = UangJalanBongkaran::with(['suratJalanBongkaran', 'createdBy']);

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('suratJalanBongkaran', function ($suratJalanQuery) use ($search) {
                      $suratJalanQuery->where('nomor_surat_jalan', 'like', "%{$search}%")
                                      ->orWhere('supir', 'like', "%{$search}%")
                                      ->orWhere('no_plat', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status
        $query->byStatus($status);

        // Filter berdasarkan tanggal
        $query->byDateRange($tanggal_dari, $tanggal_sampai);

        // Sort by created_at desc
        $query->orderBy('created_at', 'desc');

        $uangJalans = $query->paginate(15);

        $statusOptions = UangJalanBongkaran::getStatusOptions();
        $statusOptions = ['all' => 'Semua Status'] + $statusOptions;

        return view('uang-jalan-bongkaran.index', compact(
            'uangJalans',
            'search',
            'status',
            'tanggal_dari',
            'tanggal_sampai',
            'statusOptions'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suratJalanBongkaran = null;
        $nomorUangJalan = null;

        if ($request->has('surat_jalan_bongkaran_id')) {
            $suratJalanBongkaran = SuratJalanBongkaran::find($request->surat_jalan_bongkaran_id);
            
            // Calculate uang jalan from pricelist if available, otherwise use existing value
            if ($suratJalanBongkaran) {
                $calculatedUangJalan = $this->calculateUangJalanFromPricelist($suratJalanBongkaran);
                if ($calculatedUangJalan > 0) {
                    $suratJalanBongkaran->uang_jalan = $calculatedUangJalan;
                }
            }
        }

        // Generate nomor uang jalan if surat jalan is selected
        if ($suratJalanBongkaran) {
            $nomorUangJalan = UangJalanBongkaran::generateNomorUangJalan();
        }

        return view('uang-jalan-bongkaran.create', compact('suratJalanBongkaran', 'nomorUangJalan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nomor_uang_jalan' => 'nullable|string|max:255',
            'tanggal_uang_jalan' => 'required|date',
            'surat_jalan_bongkaran_id' => 'required|exists:surat_jalan_bongkarans,id',
            'kegiatan_bongkar_muat' => 'nullable|string|max:255',
            'jumlah_uang_jalan' => 'required|numeric|min:0',
            'jumlah_mel' => 'nullable|numeric|min:0',
            'jumlah_pelancar' => 'nullable|numeric|min:0',
            'jumlah_kawalan' => 'nullable|numeric|min:0',
            'jumlah_parkir' => 'nullable|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string|max:255',
            'jumlah_penyesuaian' => 'nullable|numeric',
            'memo' => 'nullable|string',
            'status' => 'required|in:belum_dibayar,belum_masuk_pranota,sudah_masuk_pranota,lunas,dibatalkan'
        ]);

        // Hitung subtotal
        $subtotal = $validatedData['jumlah_uang_jalan'] +
                   ($validatedData['jumlah_mel'] ?? 0) +
                   ($validatedData['jumlah_pelancar'] ?? 0) +
                   ($validatedData['jumlah_kawalan'] ?? 0) +
                   ($validatedData['jumlah_parkir'] ?? 0);

        // Hitung total
        $total = $subtotal + ($validatedData['jumlah_penyesuaian'] ?? 0);

        $validatedData['subtotal'] = $subtotal;
        $validatedData['jumlah_total'] = $total;
        $validatedData['created_by'] = Auth::id();

        UangJalanBongkaran::create($validatedData);

        return redirect()->route('uang-jalan-bongkaran.index')
                        ->with('success', 'Uang jalan bongkaran berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UangJalanBongkaran $uangJalanBongkaran)
    {
        return view('uang-jalan-bongkaran.show', compact('uangJalanBongkaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UangJalanBongkaran $uangJalanBongkaran)
    {
        return view('uang-jalan-bongkaran.edit', compact('uangJalanBongkaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UangJalanBongkaran $uangJalanBongkaran)
    {
        $validatedData = $request->validate([
            'nomor_uang_jalan' => 'nullable|string|max:255',
            'tanggal_uang_jalan' => 'required|date',
            'surat_jalan_bongkaran_id' => 'required|exists:surat_jalan_bongkarans,id',
            'kegiatan_bongkar_muat' => 'nullable|string|max:255',
            'jumlah_uang_jalan' => 'required|numeric|min:0',
            'jumlah_mel' => 'nullable|numeric|min:0',
            'jumlah_pelancar' => 'nullable|numeric|min:0',
            'jumlah_kawalan' => 'nullable|numeric|min:0',
            'jumlah_parkir' => 'nullable|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string|max:255',
            'jumlah_penyesuaian' => 'nullable|numeric',
            'memo' => 'nullable|string',
            'status' => 'required|in:belum_dibayar,belum_masuk_pranota,sudah_masuk_pranota,lunas,dibatalkan'
        ]);

        // Hitung subtotal
        $subtotal = $validatedData['jumlah_uang_jalan'] +
                   ($validatedData['jumlah_mel'] ?? 0) +
                   ($validatedData['jumlah_pelancar'] ?? 0) +
                   ($validatedData['jumlah_kawalan'] ?? 0) +
                   ($validatedData['jumlah_parkir'] ?? 0);

        // Hitung total
        $total = $subtotal + ($validatedData['jumlah_penyesuaian'] ?? 0);

        $validatedData['subtotal'] = $subtotal;
        $validatedData['jumlah_total'] = $total;

        $uangJalanBongkaran->update($validatedData);

        return redirect()->route('uang-jalan-bongkaran.index')
                        ->with('success', 'Uang jalan bongkaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UangJalanBongkaran $uangJalanBongkaran)
    {
        $uangJalanBongkaran->delete();

        return redirect()->route('uang-jalan-bongkaran.index')
                        ->with('success', 'Uang jalan bongkaran berhasil dihapus.');
    }

    /**
     * Show form to select surat jalan bongkaran
     */
    public function selectSuratJalanBongkaran(Request $request)
    {
        $search = $request->get('search', '');
        $tanggal_dari = $request->get('tanggal_dari', '');
        $tanggal_sampai = $request->get('tanggal_sampai', '');

        $query = SuratJalanBongkaran::query();

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan tanggal
        if ($tanggal_dari) {
            $query->whereDate('tanggal_surat_jalan', '>=', $tanggal_dari);
        }
        if ($tanggal_sampai) {
            $query->whereDate('tanggal_surat_jalan', '<=', $tanggal_sampai);
        }

        // Exclude surat jalan yang sudah memiliki uang jalan
        $existingUangJalanIds = UangJalanBongkaran::pluck('surat_jalan_bongkaran_id')->toArray();
        $query->whereNotIn('id', $existingUangJalanIds);

        $suratJalanBongkarans = $query->orderBy('created_at', 'desc')->paginate(9999);

        return view('uang-jalan-bongkaran.select-surat-jalan-bongkaran', compact(
            'suratJalanBongkarans',
            'search',
            'tanggal_dari',
            'tanggal_sampai'
        ));
    }

    /**
     * Calculate uang jalan from pricelist based on route
     */
    private function calculateUangJalanFromPricelist(SuratJalanBongkaran $suratJalanBongkaran)
    {
        // Try to find pricelist based on dari (asal) and ke (tujuan)
        $dari = $suratJalanBongkaran->tujuan_pengambilan ?? '';
        $ke = $suratJalanBongkaran->tujuan_pengiriman ?? '';
        $ukuran = $suratJalanBongkaran->size ?? '20';

        if (empty($dari) || empty($ke)) {
            return 0;
        }

        // Search for active pricelist
        $pricelist = MasterPricelistUangJalan::active()
            ->where(function ($query) use ($dari, $ke) {
                $query->where(function ($q) use ($dari, $ke) {
                    $q->where('dari', 'LIKE', "%{$dari}%")
                      ->where('ke', 'LIKE', "%{$ke}%");
                })->orWhere(function ($q) use ($dari, $ke) {
                    $q->where('dari', 'LIKE', "%{$ke}%")
                      ->where('ke', 'LIKE', "%{$dari}%");
                });
            })
            ->first();

        if ($pricelist) {
            return $pricelist->getUangJalanBySize($ukuran);
        }

        return 0;
    }
}