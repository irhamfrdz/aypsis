<?php

namespace App\Http\Controllers;

use App\Models\Pranota;
use App\Models\TagihanCat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class PranotaTagihanCatController extends Controller
{
    /**
     * Display a listing of pranota CAT
     */
    public function index(Request $request)
    {
        $query = Pranota::with(['pembayaranKontainer' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->whereNotNull('tagihan_ids')
        ->where('tagihan_ids', '!=', '[]')
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
    public function show($id)
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
    public function print($id)
    {
        $pranota = Pranota::findOrFail($id);

        // Get tagihan CAT items
        $tagihanItems = collect();
        if (!empty($pranota->tagihan_ids)) {
            $tagihanItems = \App\Models\TagihanCat::whereIn('id', $pranota->tagihan_ids)->get();
        }

        return view('pranota-cat.print', compact('pranota', 'tagihanItems'));
    }

    /**
     * Store a single pranota CAT
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

            return redirect()->route('pranota-cat.index')->with('success', 'Pranota CAT berhasil dibuat dengan nomor: ' . $pranota->no_invoice);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota CAT: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create pranota from tagihan CAT
     */
    public function bulkCreateFromTagihanCat(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:tagihan_cats,id'
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan CAT items
            $tagihanItems = TagihanCat::whereIn('id', $request->selected_ids)->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan CAT yang ditemukan dengan ID yang dipilih');
            }

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

            // Create pranota
            $pranota = Pranota::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $tagihanItems->sum('realisasi_biaya'),
                'keterangan' => 'Pranota bulk CAT untuk ' . count($request->selected_ids) . ' tagihan',
                'status' => 'unpaid',
                'tagihan_ids' => $request->selected_ids,
                'jumlah_tagihan' => count($request->selected_ids),
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Update tagihan CAT items status
            TagihanCat::whereIn('id', $request->selected_ids)
                ->update(['status' => 'paid']);

            DB::commit();

            return redirect()->back()->with('success',
                'Pranota CAT bulk berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' untuk ' . count($request->selected_ids) . ' tagihan CAT (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota CAT bulk: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status for pranota CAT
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:pranotas,id',
            'status' => 'required|string|in:unpaid,approved,in_progress,completed,cancelled'
        ]);

        try {
            Pranota::whereIn('id', $request->ids)->update([
                'status' => $request->status,
                'updated_at' => Carbon::now()
            ]);

            $statusLabels = [
                'unpaid' => 'Belum Lunas',
                'approved' => 'Disetujui',
                'in_progress' => 'Dalam Proses',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ];

            return redirect()->back()->with('success',
                count($request->ids) . ' pranota CAT berhasil diupdate status menjadi: ' . ($statusLabels[$request->status] ?? $request->status)
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update status pranota CAT: ' . $e->getMessage());
        }
    }

    /**
     * Bulk payment for pranota CAT
     */
    public function bulkPayment(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:pranotas,id'
        ]);

        try {
            $pranotaList = Pranota::whereIn('id', $request->ids)->get();

            if ($pranotaList->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada pranota yang ditemukan');
            }

            // Calculate total amount
            $totalAmount = $pranotaList->sum(function($pranota) {
                return $pranota->calculateTotalAmount();
            });

            // Store in session for payment form
            session([
                'bulk_payment_pranota_cat_ids' => $request->ids,
                'bulk_payment_pranota_cat_total' => $totalAmount,
                'bulk_payment_pranota_cat_count' => count($request->ids)
            ]);

            return redirect()->route('pembayaran-pranota-cat.create')->with('info',
                'Siap melakukan pembayaran untuk ' . count($request->ids) . ' pranota CAT dengan total Rp ' . number_format($totalAmount, 0, ',', '.')
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran bulk: ' . $e->getMessage());
        }
    }
}
