<?php

namespace App\Http\Controllers;

use App\Models\Pranota;
use App\Models\DaftarTagihanKontainerSewa;
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
            'keterangan' => 'nullable|string',
            'nomor_cetakan' => 'nullable|integer|min:1|max:9' // Optional, default 1
        ]);

        try {
            DB::beginTransaction();

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

            $pranota = Pranota::create([
                'no_invoice' => $noInvoice,
                'keterangan' => $request->keterangan,
                'status' => 'draft',
                'tagihan_ids' => [], // Empty for single creation
                'jumlah_tagihan' => 0,
                'total_amount' => 0,
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pranota berhasil dibuat dengan nomor: ' . $pranota->no_invoice);

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
                'status' => 'draft',
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
                ' untuk ' . count($request->selected_ids) . ' tagihan (Total: Rp ' . number_format($pranota->total_amount, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota bulk: ' . $e->getMessage());
        }
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
            'status' => 'required|in:draft,sent,paid,cancelled'
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
            case 'draft':
                return 'included';
            case 'sent':
                return 'invoiced';
            case 'paid':
                return 'paid';
            case 'cancelled':
                return 'cancelled';
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
            case 'draft':
                return 'Draft';
            case 'sent':
                return 'Terkirim';
            case 'paid':
                return 'Lunas';
            case 'cancelled':
                return 'Dibatalkan';
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
}
