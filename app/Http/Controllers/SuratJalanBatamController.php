<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratJalanBatam;
use App\Models\OrderBatam;
use App\Models\Karyawan;
use App\Models\StockKontainer;
use App\Models\Kontainer;
use App\Models\PricelistUangJalanBatam;

class SuratJalanBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratJalanBatam::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhere('tipe_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by status pembayaran
        if ($request->filled('status_pembayaran') && $request->status_pembayaran !== 'all') {
            $statusPembayaran = $request->status_pembayaran;
            $query->where(function($q) use ($statusPembayaran) {
                if ($statusPembayaran === 'sudah_dibayar') {
                    $q->where('status_pembayaran', 'sudah_dibayar')
                      ->orWhere('status_pembayaran_uang_jalan', 'dibayar');
                } elseif ($statusPembayaran === 'belum_dibayar') {
                    $q->where('status_pembayaran_uang_jalan', 'sudah_masuk_uang_jalan')
                      ->where('status_pembayaran', '!=', 'sudah_dibayar');
                } else { // belum_masuk_pranota
                    $q->where('status_pembayaran_uang_jalan', 'belum_ada')
                      ->where('status_pembayaran', '!=', 'sudah_dibayar');
                }
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        $suratJalans = $query->with('orderBatam')
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        return view('surat-jalan-batam.index', compact('suratJalans'));
    }

    /**
     * Show order selection page before creating surat jalan batam.
     */
    public function selectOrder(Request $request)
    {
        $query = \App\Models\OrderBatam::whereIn('status', ['active', 'confirmed', 'processing']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', "%{$search}%")
                  ->orWhere('tujuan_kirim', 'like', "%{$search}%")
                  ->orWhere('tujuan_ambil', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('tanggal_order', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(15);

        return view('surat-jalan-batam.select-order', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedOrder = null;
        if ($request->filled('order_id')) {
            $selectedOrder = \App\Models\OrderBatam::with(['pengirim', 'jenisBarang', 'tujuanAmbil', 'term'])
                                  ->find($request->order_id);
        }

        $supirs = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->get();
        $keneks = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'KENEK')->get();
        // Just a basic list for now, ideally we'd filter for supir/kenek roles
        
        // Calculate default uang jalan from pricelist
        $defaultUangJalan = 0;
        if ($selectedOrder) {
            $pricelist = PricelistUangJalanBatam::where('rute', $selectedOrder->tujuan_ambil)
                ->where('size', $selectedOrder->size_kontainer)
                ->where('f_e', $selectedOrder->f_e)
                ->first();
            
            if ($pricelist) {
                $defaultUangJalan = $pricelist->tarif;
            }
        }

        // Get normalized ukuran kontainer (normalize 20, 20ft, 20FT to 20FT)
        $ukuranKontainers = StockKontainer::select('ukuran')
            ->distinct()
            ->whereNotNull('ukuran')
            ->get()
            ->map(function($item) {
                $val = strtoupper(str_replace(['ft', 'FT', ' '], '', $item->ukuran));
                return $val ? $val . 'FT' : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Get unique container numbers from Stock AND Kontainer (Sewa)
        $stockNos = StockKontainer::whereNotNull('nomor_seri_gabungan')->pluck('nomor_seri_gabungan')->toArray();
        $sewaNos = Kontainer::whereNotNull('nomor_seri_gabungan')->pluck('nomor_seri_gabungan')->toArray();
        $daftarKontainers = collect(array_merge($stockNos, $sewaNos))->unique()->sort()->values()->toArray();

        return view('surat-jalan-batam.create', compact('selectedOrder', 'supirs', 'keneks', 'defaultUangJalan', 'ukuranKontainers', 'daftarKontainers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_batam_id' => 'nullable|exists:order_batams,id',
            'no_surat_jalan' => 'required|unique:surat_jalan_batams,no_surat_jalan',
            'tanggal_surat_jalan' => 'required|date',
            'pengirim' => 'nullable|string',
            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'jenis_barang' => 'nullable|string',
            'tipe_kontainer' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'kenek' => 'nullable|string',
            'uang_jalan' => 'nullable|string',
            'status' => 'required|in:draft,active,completed,cancelled',
            'size' => 'nullable|string', // Added validation rule for 'size'
        ]);

        if ($request->filled('uang_jalan')) {
            $validated['uang_jalan'] = (float) str_replace(['.', ','], ['', '.'], $request->uang_jalan);
        }

        $validated['input_by'] = Auth::id();
        $validated['input_date'] = now();

        $suratJalan = SuratJalanBatam::create($validated);

        // Optional: Update order progress if linked
        if ($suratJalan->order_batam_id) {
            $order = $suratJalan->orderBatam;
            if ($order && $order->isOutstanding()) {
                $order->processUnits(1, 'Dibuat Surat Jalan Batam: ' . $suratJalan->no_surat_jalan);
            }
        }

        return redirect()->route('surat-jalan-batam.index')->with('success', 'Surat Jalan Batam berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $suratJalan = SuratJalanBatam::with('orderBatam')->findOrFail($id);
        return view('surat-jalan-batam.show', compact('suratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);
        $supirs = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->get();
        $keneks = \App\Models\Karyawan::where('status', 'active')->where('divisi', 'KENEK')->get();
        
        $ukuranKontainers = StockKontainer::select('ukuran')
            ->distinct()
            ->whereNotNull('ukuran')
            ->get()
            ->map(function($item) {
                $val = strtoupper(str_replace(['ft', 'FT', ' '], '', $item->ukuran));
                return $val ? $val . 'FT' : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $stockNos = StockKontainer::whereNotNull('nomor_seri_gabungan')->pluck('nomor_seri_gabungan')->toArray();
        $sewaNos = Kontainer::whereNotNull('nomor_seri_gabungan')->pluck('nomor_seri_gabungan')->toArray();
        $daftarKontainers = collect(array_merge($stockNos, $sewaNos))->unique()->sort()->values()->toArray();

        return view('surat-jalan-batam.edit', compact('suratJalan', 'supirs', 'keneks', 'ukuranKontainers', 'daftarKontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);
        
        $validated = $request->validate([
            'no_surat_jalan' => 'required|unique:surat_jalan_batams,no_surat_jalan,' . $id,
            'tanggal_surat_jalan' => 'required|date',
            'pengirim' => 'nullable|string',
            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'jenis_barang' => 'nullable|string',
            'tipe_kontainer' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'kenek' => 'nullable|string',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);

        $suratJalan->update($validated);

        return redirect()->route('surat-jalan-batam.index')->with('success', 'Surat Jalan Batam berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);
        $suratJalan->delete();

        return redirect()->route('surat-jalan-batam.index')->with('success', 'Surat Jalan Batam berhasil dihapus');
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->can('surat-jalan-batam-approve')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $suratJalan = SuratJalanBatam::findOrFail($id);
        $suratJalan->status = $request->status;
        $suratJalan->save();

        return response()->json(['success' => true]);
    }

    public function print($id)
    {
        $suratJalan = SuratJalanBatam::with('orderBatam')->findOrFail($id);
        // This would normally return a PDF or a print view
        return view('surat-jalan-batam.print', compact('suratJalan'));
    }

    public function printMemo($id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);
        return view('surat-jalan-batam.print-memo', compact('suratJalan'));
    }

    public function printPreprinted($id)
    {
        $suratJalan = SuratJalanBatam::findOrFail($id);
        return view('surat-jalan-batam.print-preprinted', compact('suratJalan'));
    }

    public function generateSuratJalanBatamNumber(Request $request)
    {
        $date = $request->date ? \Carbon\Carbon::parse($request->date) : now();
        $year = $date->format('Y');
        $month = $date->format('m');
        
        $lastSj = SuratJalanBatam::whereYear('tanggal_surat_jalan', $year)
                                 ->whereMonth('tanggal_surat_jalan', $month)
                                 ->orderBy('no_surat_jalan', 'desc')
                                 ->first();

        $nextNumber = 1;
        if ($lastSj) {
            $parts = explode('/', $lastSj->no_surat_jalan);
            $lastNum = (int) end($parts);
            $nextNumber = $lastNum + 1;
        }

        $formattedNumber = "SJB/{$year}/{$month}/" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['number' => $formattedNumber]);
    }
}
