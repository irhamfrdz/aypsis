<?php

namespace App\Http\Controllers;

use App\Models\PranotaTagihanKontainerSewa;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PranotaTagihanKontainerSewaController extends Controller
{
    /**
     * Display a listing of pranota kontainer sewa
     */
    public function index(Request $request)
    {
        $query = PranotaTagihanKontainerSewa::orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('no_invoice', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }

        $pranotaList = $query->paginate(15);

        return view('pranota.index', compact('pranotaList'));
    }

    /**
     * Show the form for creating a new pranota kontainer sewa
     */
    public function create()
    {
        // For pranota kontainer sewa, creation is typically done via bulk operations
        // from the tagihan kontainer sewa page. This create form is for manual creation.
        return view('pranota.create', [
            'tagihanCat' => null,
            'nomorPranota' => 'PTKS' . date('ym') . '000001',
            'catatan' => 'Pranota kontainer sewa manual'
        ]);
    }

    /**
     * Store a newly created pranota kontainer sewa
     */
    public function store(Request $request)
    {
        $request->validate([
            'tagihan_kontainer_sewa_ids' => 'required|array|min:1',
            'tagihan_kontainer_sewa_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id',
            'keterangan' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'due_date' => 'nullable|date|after:today'
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_kontainer_sewa_ids)->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan kontainer sewa yang ditemukan dengan ID yang dipilih');
            }

            // Generate nomor pranota dengan format PMS dari master nomor terakhir
            $nomorCetakan = 1; // Default
            $tahun = Carbon::now()->format('y'); // 2 digit year
            $bulan = Carbon::now()->format('m'); // 2 digit month

            // Get next nomor pranota from master nomor terakhir dengan modul PMS
            $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
            if (!$nomorTerakhir) {
                throw new \Exception('Modul PMS tidak ditemukan di master nomor terakhir.');
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();

            // Create pranota
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $tagihanItems->sum('grand_total'),
                'keterangan' => $request->keterangan ?? 'Pranota kontainer sewa untuk ' . count($request->tagihan_kontainer_sewa_ids) . ' tagihan',
                'supplier' => $request->supplier,
                'status' => 'unpaid',
                'tagihan_kontainer_sewa_ids' => $request->tagihan_kontainer_sewa_ids,
                'jumlah_tagihan' => count($request->tagihan_kontainer_sewa_ids),
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => $request->due_date ?? Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Update tagihan items status
            DaftarTagihanKontainerSewa::whereIn('id', $request->tagihan_kontainer_sewa_ids)
                ->update(['status' => 'paid']);

            DB::commit();

            return redirect()->route('pranota.index')->with('success',
                'Pranota kontainer sewa berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota kontainer sewa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified pranota kontainer sewa
     */
    public function show(PranotaTagihanKontainerSewa $pranota)
    {
        $tagihanItems = $pranota->tagihanKontainerSewaItems();
        return view('pranota.show', compact('pranota', 'tagihanItems'));
    }

    /**
     * Print pranota kontainer sewa
     */
    public function print(PranotaTagihanKontainerSewa $pranota)
    {
        $tagihanItems = $pranota->tagihanKontainerSewaItems();
        return view('pranota.print', compact('pranota', 'tagihanItems'));
    }

    /**
     * Bulk create from tagihan kontainer sewa
     */
    public function bulkCreateFromTagihanKontainerSewa(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id'
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan kontainer sewa items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->selected_ids)->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan kontainer sewa yang ditemukan dengan ID yang dipilih');
            }

            // Generate nomor pranota dengan format PMS dari master nomor terakhir
            $nomorCetakan = 1; // Default
            $tahun = Carbon::now()->format('y'); // 2 digit year
            $bulan = Carbon::now()->format('m'); // 2 digit month

            // Get next nomor pranota from master nomor terakhir dengan modul PMS
            $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
            if (!$nomorTerakhir) {
                throw new \Exception('Modul PMS tidak ditemukan di master nomor terakhir.');
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();

            // Create pranota
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $tagihanItems->sum('grand_total'),
                'keterangan' => 'Pranota bulk kontainer sewa untuk ' . count($request->selected_ids) . ' tagihan',
                'status' => 'unpaid',
                'tagihan_kontainer_sewa_ids' => $request->selected_ids,
                'jumlah_tagihan' => count($request->selected_ids),
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Create corresponding Pranota record for backward compatibility with views
            $legacyPranota = \App\Models\Pranota::create([
                'no_invoice' => $noInvoice,
                'total_amount' => $tagihanItems->sum('grand_total'),
                'keterangan' => 'Pranota bulk kontainer sewa untuk ' . count($request->selected_ids) . ' tagihan',
                'status' => 'unpaid',
                'tagihan_ids' => $request->selected_ids,
                'jumlah_tagihan' => count($request->selected_ids),
                'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
            ]);

            // Update tagihan kontainer sewa items status and pranota relationship
            DaftarTagihanKontainerSewa::whereIn('id', $request->selected_ids)
                ->update([
                    'status' => 'paid',
                    'status_pranota' => 'included',
                    'pranota_id' => $legacyPranota->id
                ]);

            DB::commit();

            return redirect()->back()->with('success',
                'Pranota kontainer sewa bulk berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                ' untuk ' . count($request->selected_ids) . ' tagihan kontainer sewa (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat pranota kontainer sewa bulk: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status for pranota kontainer sewa
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:pranota_tagihan_kontainer_sewas,id',
            'status' => 'required|string|in:unpaid,approved,in_progress,completed,cancelled'
        ]);

        try {
            PranotaTagihanKontainerSewa::whereIn('id', $request->ids)->update([
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
                count($request->ids) . ' pranota kontainer sewa berhasil diupdate status menjadi: ' . ($statusLabels[$request->status] ?? $request->status)
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update status pranota kontainer sewa: ' . $e->getMessage());
        }
    }

    /**
     * Bulk payment for pranota kontainer sewa
     */
    public function bulkPayment(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:pranota_tagihan_kontainer_sewas,id'
        ]);

        try {
            $pranotaList = PranotaTagihanKontainerSewa::whereIn('id', $request->ids)->get();

            if ($pranotaList->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada pranota yang ditemukan');
            }

            // Calculate total amount
            $totalAmount = $pranotaList->sum(function($pranota) {
                return $pranota->calculateTotalAmount();
            });

            // Store in session for payment form
            session([
                'bulk_payment_pranota_kontainer_sewa_ids' => $request->ids,
                'bulk_payment_pranota_kontainer_sewa_total' => $totalAmount,
                'bulk_payment_pranota_kontainer_sewa_count' => count($request->ids)
            ]);

            return redirect()->route('pembayaran-pranota-kontainer.create')->with('info',
                'Siap melakukan pembayaran untuk ' . count($request->ids) . ' pranota kontainer sewa dengan total Rp ' . number_format($totalAmount, 0, ',', '.')
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran bulk: ' . $e->getMessage());
        }
    }

    /**
     * Get the next pranota number for display in modal
     */
    public function getNextPranotaNumber(Request $request)
    {
        try {
            // Generate nomor pranota dengan format PMS dari master nomor terakhir
            $nomorCetakan = 1; // Default
            $tahun = Carbon::now()->format('y'); // 2 digit year
            $bulan = Carbon::now()->format('m'); // 2 digit month

            // Get next nomor pranota from master nomor terakhir dengan modul PMS
            $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->first();
            $nextNumber = $nomorTerakhir ? $nomorTerakhir->nomor_terakhir + 1 : 1;

            $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'nomor_pranota' => $noInvoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan nomor pranota: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lepas kontainer dari pranota
     */
    public function lepasKontainer(Request $request, $id)
    {
        Log::info('Lepas kontainer called', ['id' => $id, 'request' => $request->all()]);

        $request->validate([
            'tagihan_ids' => 'required|array',
            // 'tagihan_ids.*' => 'exists:daftar_tagihan_kontainer_sewa,id'
        ]);

        try {
            DB::beginTransaction();

            $pranota = PranotaTagihanKontainerSewa::findOrFail($id);
            $tagihanIds = $request->tagihan_ids;

            Log::info('Lepas kontainer validation', [
                'pranota_id' => $id,
                'tagihan_ids_request' => $tagihanIds,
                'pranota_tagihan_ids' => $pranota->tagihan_kontainer_sewa_ids
            ]);

            // Validasi bahwa tagihan IDs ada di pranota
            $currentTagihanIds = $pranota->tagihan_kontainer_sewa_ids ?? [];
            $validTagihanIds = array_intersect($tagihanIds, $currentTagihanIds);

            if (empty($validTagihanIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan yang dipilih tidak ditemukan di pranota ini.'
                ], 400);
            }

            // Get tagihan data to update kontainer status
            $tagihans = DaftarTagihanKontainerSewa::whereIn('id', $validTagihanIds)->get();

            // Update status tagihan menjadi belum masuk pranota
            // Update each tagihan individually to ensure proper handling
            DaftarTagihanKontainerSewa::whereIn('id', $validTagihanIds)->each(function ($tagihan) {
                $tagihan->update([
                    'status_pranota' => null,
                    'pranota_id' => null
                ]);
            });

            // Update status kontainer menjadi "belum masuk pranota"
            foreach ($tagihans as $tagihan) {
                if (!empty($tagihan->nomor_kontainer)) {
                    \App\Models\Kontainer::where('nomor_seri_gabungan', $tagihan->nomor_kontainer)
                        ->update(['status' => 'belum masuk pranota']);
                }
            }

            // Update pranota: hapus tagihan_ids yang dilepas
            $currentTagihanIds = $pranota->tagihan_kontainer_sewa_ids ?? [];
            $remainingTagihanIds = array_diff($currentTagihanIds, $validTagihanIds);
            $pranota->tagihan_kontainer_sewa_ids = array_values($remainingTagihanIds);
            $pranota->jumlah_tagihan = count($remainingTagihanIds);

            if (!empty($remainingTagihanIds)) {
                $pranota->total_amount = DaftarTagihanKontainerSewa::whereIn('id', $remainingTagihanIds)->sum('grand_total');
            } else {
                $pranota->total_amount = 0;
            }

            $pranota->save();

            // Refresh model untuk memastikan data terbaru
            $pranota->refresh();

            DB::commit();

            // Log untuk debug
            Log::info('Lepas kontainer berhasil', [
                'pranota_id' => $id,
                'tagihan_ids_dilepas' => $validTagihanIds,
                'remaining_tagihan_ids' => $remainingTagihanIds,
                'jumlah_tagihan_baru' => $pranota->jumlah_tagihan,
                'total_amount_baru' => $pranota->total_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => count($validTagihanIds) . ' kontainer berhasil dilepas dari pranota.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            $validTagihanIds = $validTagihanIds ?? [];
            Log::error('Lepas kontainer gagal', [
                'pranota_id' => $id,
                'tagihan_ids' => $request->tagihan_ids,
                'valid_tagihan_ids' => $validTagihanIds,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melepas kontainer: ' . $e->getMessage()
            ], 500);
        }
    }
}
