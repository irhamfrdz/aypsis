<?php

namespace App\Http\Controllers;

use App\Models\Pranota;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\TagihanCat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PranotaController extends Controller
{
    /**
     * Store a single pranota
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_invoice' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'tagihan_cat_id' => 'nullable|exists:tagihan_cats,id'
        ]);

        try {
            DB::beginTransaction();

            $noInvoice = $request->input('no_invoice');
            $tagihanCatId = $request->input('tagihan_cat_id');

            if (!$noInvoice) {
                // Generate nomor pranota with format: PTK + 1 digit cetakan + 2 digit tahun + 2 digit bulan + 6 digit running number
                $nomorCetakan = 1; // Default
                $tahun = Carbon::now()->format('y'); // 2 digit year
                $bulan = Carbon::now()->format('m'); // 2 digit month

                // Running number: count pranota in current month + 1
                $runningNumber = str_pad(
                    Pranota::whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->count() + 1,
                    6, '0', STR_PAD_LEFT
                );

                $noInvoice = "PTK{$nomorCetakan}{$tahun}{$bulan}{$runningNumber}";
            }

            $tagihanIds = [];
            $totalAmount = 0;
            $jumlahTagihan = 0;

            if ($tagihanCatId) {
                $tagihanCat = TagihanCat::find($tagihanCatId);
                if ($tagihanCat) {
                    $tagihanIds = [$tagihanCatId];
                    $totalAmount = $tagihanCat->realisasi_biaya ?? $tagihanCat->estimasi_biaya ?? 0;
                    $jumlahTagihan = 1;

                    // Update status tagihan CAT
                    $tagihanCat->update(['status' => 'paid']);
                }
            }

            $pranota = Pranota::create([
                'no_invoice' => $noInvoice,
                'keterangan' => $request->keterangan,
                'status' => 'unpaid',
                'tagihan_ids' => $tagihanIds,
                'jumlah_tagihan' => $jumlahTagihan,
                'total_amount' => $totalAmount,
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            DB::commit();

            return redirect()->route('pranota.index')->with('success', 'Pranota berhasil dibuat dengan nomor: ' . $pranota->no_invoice);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota: ' . $e->getMessage());
        }
    }

    /**
     * Store bulk pranota from selected tagihan items
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id',
            'nomor_cetakan' => 'nullable|integer|min:1|max:9' // Optional, default 1
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->selected_ids)->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan yang ditemukan dengan ID yang dipilih');
            }

            // Generate nomor pranota with format: PTK + 1 digit cetakan + 2 digit tahun + 2 digit bulan + 6 digit running number
            $nomorCetakan = $request->input('nomor_cetakan', 1);
            $tahun = Carbon::now()->format('y'); // 2 digit year
            $bulan = Carbon::now()->format('m'); // 2 digit month

            // Running number: count pranota in current month + 1
            $runningNumber = str_pad(
                Pranota::whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->count() + 1,
                6, '0', STR_PAD_LEFT
            );

            $noInvoice = "PTK{$nomorCetakan}{$tahun}{$bulan}{$runningNumber}";

            // Create pranota
            $pranota = Pranota::create([
                'no_invoice' => $noInvoice,
                'total_amount' => 0, // Will be calculated and updated below
                'keterangan' => 'Pranota bulk untuk ' . count($request->selected_ids) . ' tagihan',
                'status' => 'unpaid',
                'tagihan_ids' => $request->selected_ids,
                'jumlah_tagihan' => count($request->selected_ids),
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Update total amount using model method
            $pranota->updateTotalAmount();

            // Update tagihan items to mark them as included in pranota
            DaftarTagihanKontainerSewa::whereIn('id', $request->selected_ids)
                ->update([
                    'status_pranota' => 'included',
                    'pranota_id' => $pranota->id,
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();

            return redirect()->back()->with('success',
                'Pranota bulk berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' untuk ' . count($request->selected_ids) . ' tagihan (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota bulk: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create pranota from tagihan CAT
     */
    public function bulkCreateFromTagihanCat(Request $request)
    {
        $request->validate([
            'tagihan_cat_ids' => 'required|array|min:1',
            'tagihan_cat_ids.*' => 'exists:tagihan_cat,id',
            'nomor_pranota' => 'required|string',
            'tanggal_pranota' => 'required|date',
            'supplier' => 'required|string',
            'realisasi_biaya_total' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan CAT items
            $tagihanCatItems = TagihanCat::whereIn('id', $request->tagihan_cat_ids)->get();

            if ($tagihanCatItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan CAT yang ditemukan dengan ID yang dipilih');
            }

            // Create pranota with data from form
            $pranota = Pranota::create([
                'no_invoice' => $request->nomor_pranota,
                'total_amount' => $request->realisasi_biaya_total,
                'keterangan' => $request->keterangan ?: 'Pranota untuk tagihan CAT - ' . $tagihanCatItems->pluck('nomor_kontainer')->join(', '),
                'supplier' => $request->supplier,
                'status' => 'unpaid',
                'tagihan_ids' => $request->tagihan_cat_ids,
                'jumlah_tagihan' => count($request->tagihan_cat_ids),
                'tanggal_pranota' => $request->tanggal_pranota,
                'due_date' => Carbon::parse($request->tanggal_pranota)->addDays(30)->format('Y-m-d')
            ]);

            // Attach tagihan CAT items to pranota via pivot table
            $pranota->tagihanCatItems()->attach($request->tagihan_cat_ids);

            // Update tagihan CAT items to mark them as included in pranota
            TagihanCat::whereIn('id', $request->tagihan_cat_ids)
                ->update([
                    'status' => 'masuk pranota',
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();

            return redirect()->back()->with('success',
                'Pranota berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' untuk ' . count($request->tagihan_cat_ids) . ' tagihan CAT (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Error creating pranota from tagihan CAT: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal membuat pranota dari tagihan CAT: ' . $e->getMessage());
        }
    }

    /**
     * Show form to create new pranota
     */
    public function create(Request $request)
    {
        $tagihanCatId = $request->query('tagihan_cat_id');
        $nomorPranota = $request->query('nomor_pranota');
        $catatan = $request->query('catatan');

        $tagihanCat = null;
        if ($tagihanCatId) {
            $tagihanCat = TagihanCat::find($tagihanCatId);
        }

        return view('pranota.create', compact('tagihanCat', 'nomorPranota', 'catatan'));
    }

    /**
     * Display pranota list
     */
    public function index()
    {
        $pranotaList = Pranota::with(['pembayaranKontainer' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->orderBy('created_at', 'desc')->paginate(15);

        return view('pranota.index', compact('pranotaList'));
    }

    /**
     * Show specific pranota details
     */
    public function show($id)
    {
        $pranota = Pranota::findOrFail($id);

        // Direct query instead of using model method to avoid undefined method error
        $tagihanItems = collect();
        if (!empty($pranota->tagihan_ids)) {
            $tagihanItems = \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $pranota->tagihan_ids)->get();
        }

        return view('pranota.show', compact('pranota', 'tagihanItems'));
    }

    /**
     * Update pranota status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:unpaid,paid'
        ]);

        try {
            DB::beginTransaction();

            $pranota = Pranota::findOrFail($id);
            $oldStatus = $pranota->status;
            $newStatus = $request->status;

            $pranota->status = $newStatus;
            $pranota->save();

            // Update status tagihan berdasarkan status pranota
            if (!empty($pranota->tagihan_ids)) {
                $tagihanStatus = $this->getTagihanStatusFromPranota($newStatus);

                DaftarTagihanKontainerSewa::whereIn('id', $pranota->tagihan_ids)
                    ->update([
                        'status_pranota' => $tagihanStatus,
                        'updated_at' => Carbon::now()
                    ]);
            }

            DB::commit();

            $statusText = $this->getStatusText($newStatus);
            return redirect()->back()->with('success', "Status pranota berhasil diupdate menjadi: {$statusText}");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }

    /**
     * Get tagihan status based on pranota status
     */
    private function getTagihanStatusFromPranota($pranotaStatus)
    {
        switch ($pranotaStatus) {
            case 'unpaid':
                return 'included';
            case 'paid':
                return 'paid';
            default:
                return 'included';
        }
    }

    /**
     * Get status text for display
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 'unpaid':
                return 'Belum Lunas';
            case 'paid':
                return 'Lunas';
            default:
                return ucfirst($status);
        }
    }

    /**
     * Delete pranota
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pranota = Pranota::findOrFail($id);

            // Reset tagihan items status
            if (!empty($pranota->tagihan_ids)) {
                DaftarTagihanKontainerSewa::whereIn('id', $pranota->tagihan_ids)
                    ->update([
                        'status_pranota' => null,
                        'pranota_id' => null,
                        'updated_at' => Carbon::now()
                    ]);
            }

            $pranota->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Pranota berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus pranota: ' . $e->getMessage());
        }
    }

    /**
     * Print pranota
     */
    public function print($id)
    {
        $pranota = Pranota::findOrFail($id);

        // Direct query instead of using model method to avoid undefined method error
        $tagihanItems = collect();
        if (!empty($pranota->tagihan_ids)) {
            $tagihanItems = \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $pranota->tagihan_ids)->get();
        }

        return view('pranota.print', compact('pranota', 'tagihanItems'));
    }

    /**
     * Display pranota for tagihan CAT
     */
    public function indexCat(Request $request)
    {
        $query = Pranota::with(['pembayaranKontainer' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->whereNotNull('tagihan_ids') // Only pranota that have tagihan_ids
        ->where('tagihan_ids', '!=', '[]') // Exclude empty arrays
        ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pranota', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pranota', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_invoice', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pranotaCats = $query->paginate(15)->appends($request->query());

        return view('pranota-cat.index', compact('pranotaCats'));
    }

    /**
     * Show specific pranota CAT details
     */
    public function showCat($id)
    {
        $pranota = Pranota::findOrFail($id);

        // Get tagihan CAT items
        $tagihanItems = collect();
        if (!empty($pranota->tagihan_ids)) {
            $tagihanItems = \App\Models\TagihanCat::whereIn('id', $pranota->tagihan_ids)->get();
        }

        return view('pranota-cat.show', compact('pranota', 'tagihanItems'));
    }

    /**
     * Print pranota CAT
     */
    public function printCat($id)
    {
        $pranota = Pranota::findOrFail($id);

        // Get tagihan CAT items
        $tagihanItems = collect();
        if (!empty($pranota->tagihan_ids)) {
            $tagihanItems = \App\Models\TagihanCat::whereIn('id', $pranota->tagihan_ids)->get();
        }

        return view('pranota-cat.print', compact('pranota', 'tagihanItems'));
    }
}
