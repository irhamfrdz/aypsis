<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaLembur;
use App\Models\PranotaLembur;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranPranotaLemburController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-lembur-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-lembur-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-lembur-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-lembur-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PranotaLembur::with([
            'pembayaranPranotaLemburs', 
            'creator', 
            'updater'
        ]);

        // Filter by status pembayaran
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_pranota', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        // Search by pranota number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nomor_pranota', 'like', "%{$search}%");
        }

        $pranotaList = $query->orderBy('tanggal_pranota', 'desc')->paginate(15);

        $statuses = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            'cancelled' => 'Dibatalkan'
        ];

        return view('pembayaran-pranota-lembur.index', compact('pranotaList', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get approved pranota lembur that are not yet paid
        $pranotaLemburQuery = PranotaLembur::query();

        // Only show not paid and not cancelled pranota
        $pranotaLemburQuery->where('status', '!=', PranotaLembur::STATUS_CANCELLED)
            ->whereDoesntHave('pembayaranPranotaLemburs');

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaLemburQuery->whereBetween('tanggal_pranota', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // If pranota_id is provided, filter for specific pranota
        if ($request->filled('pranota_id')) {
            $pranotaLemburQuery->where('id', $request->pranota_id);
        }

        $pranotaLemburs = $pranotaLemburQuery
            ->with(['suratJalans', 'suratJalanBongkarans'])
            ->orderBy('tanggal_pranota', 'desc')
            ->get();

        // Get akun COA for bank selection
        $akunCoa = Coa::where(function($q) {
                        $q->where('tipe_akun', 'LIKE', '%bank%')
                          ->orWhere('nama_akun', 'LIKE', '%bank%')
                          ->orWhere('nama_akun', 'LIKE', '%kas%');
                      })
                      ->orderBy('nama_akun')
                      ->get();

        // Generate nomor pembayaran using SIS modul
        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-lembur.create', compact('pranotaLemburs', 'nomorPembayaran', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_lembur_ids' => ['required', 'array', 'min:1'],
            'pranota_lembur_ids.*' => ['exists:pranota_lemburs,id'],
            'nomor_pembayaran' => 'nullable|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['Debit', 'Kredit'])],
            'bank' => 'required|string|max:255',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'nomor_cetakan' => 'nullable|integer|min:1|max:9'
        ]);

        DB::beginTransaction();

        try {
            // Get SIS modul from nomor_terakhir
            $modulSis = NomorTerakhir::where('modul', 'SIS')->firstOrFail();
            
            // Generate new payment number and increment
            $nomorPembayaran = $this->generateNomorPembayaranSIS();
            
            // Update nomor_terakhir for SIS modul
            $modulSis->increment('nomor_terakhir');

            // Prepare payment data
            $paymentData = $validated;
            unset($paymentData['pranota_lembur_ids']);
            
            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = PembayaranPranotaLembur::STATUS_PAID;
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            // Create ONE payment record
            $pembayaran = PembayaranPranotaLembur::create($paymentData);

            // Process each selected pranota
            foreach ($validated['pranota_lembur_ids'] as $pranotaId) {
                $pranota = PranotaLembur::findOrFail($pranotaId);
                
                // Attach pranota to payment via pivot table
                $pembayaran->pranotaLemburs()->attach($pranotaId, [
                    'subtotal' => $pranota->total_setelah_adjustment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update pranota status to paid
                $pranota->update([
                    'status' => PranotaLembur::STATUS_PAID,
                    'updated_by' => Auth::id()
                ]);
            }

            // Double-entry accounting
            $totalPembayaran = $validated['total_tagihan_setelah_penyesuaian'] ?? $validated['total_pembayaran'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $keterangan = "Pembayaran Pranota Lembur - " . $nomorPembayaran;

            // Use Biaya Lembur Jakarta as default if not found
            $biayaAccount = 'Biaya Lembur Jakarta';
            if (!Coa::where('nama_akun', $biayaAccount)->exists()) {
                // Fallback to searching any Biaya Lembur if Jakarta doesn't exist
                $fallbackCoa = Coa::where('nama_akun', 'LIKE', 'Biaya Lembur%')->first();
                if ($fallbackCoa) {
                    $biayaAccount = $fallbackCoa->nama_akun;
                }
            }

            if ($jenisTransaksi == 'Debit') {
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    ['nama_akun' => $biayaAccount, 'jumlah' => $totalPembayaran],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Lembur',
                    $keterangan
                );
            } else {
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $biayaAccount, 'jumlah' => $totalPembayaran],
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Lembur',
                    $keterangan
                );
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-lembur.index')
                           ->with('success', 'Pembayaran ' . $nomorPembayaran . ' berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Lembur: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranPranotaLembur $pembayaranPranotaLembur)
    {
        $pembayaranPranotaLembur->load(['pranotaLemburs', 'createdBy', 'updatedBy']);
        return view('pembayaran-pranota-lembur.show', compact('pembayaranPranotaLembur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranPranotaLembur $pembayaranPranotaLembur)
    {
        if ($pembayaranPranotaLembur->isCancelled()) {
            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('error', 'Pembayaran yang dibatalkan tidak dapat diubah.');
        }

        $pembayaranPranotaLembur->load(['pranotaLemburs.suratJalans', 'pranotaLemburs.suratJalanBongkarans']);

        $akunCoa = Coa::where(function($q) {
                        $q->where('tipe_akun', 'LIKE', '%bank%')
                          ->orWhere('nama_akun', 'LIKE', '%bank%')
                          ->orWhere('nama_akun', 'LIKE', '%kas%');
                      })
                      ->orderBy('nama_akun')
                      ->get();

        return view('pembayaran-pranota-lembur.edit', compact('pembayaranPranotaLembur', 'akunCoa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranPranotaLembur $pembayaranPranotaLembur)
    {
        if ($pembayaranPranotaLembur->isCancelled()) {
            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('error', 'Pembayaran yang dibatalkan tidak dapat diubah.');
        }

        $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $pembayaranPranotaLembur->update([
                'nomor_accurate' => $request->nomor_accurate,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('success', 'Pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating Pembayaran Pranota Lembur: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui pembayaran.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranPranotaLembur $pembayaranPranotaLembur)
    {
        if ($pembayaranPranotaLembur->isPaid()) {
            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('error', 'Pembayaran yang sudah lunas tidak dapat dihapus.');
        }

        DB::beginTransaction();

        try {
            // Update pranota status back to approved
            foreach ($pembayaranPranotaLembur->pranotaLemburs as $pranota) {
                $pranota->update(['status' => PranotaLembur::STATUS_APPROVED]);
            }

            // Delete COA transactions
            $this->coaTransactionService->deleteTransactionByReference($pembayaranPranotaLembur->nomor_pembayaran);

            $pembayaranPranotaLembur->delete();

            DB::commit();

            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('success', 'Pembayaran berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Pembayaran Pranota Lembur: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pembayaran.');
        }
    }

    /**
     * Generate nomor pembayaran menggunakan modul SIS
     */
    private function generateNomorPembayaranSIS()
    {
        $modulSis = NomorTerakhir::firstOrCreate(
            ['modul' => 'SIS'],
            [
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor Pembayaran'
            ]
        );

        $now = now();
        $bulan = $now->format('m');
        $tahun = $now->format('y');
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);
        
        return "SIS-{$bulan}-{$tahun}-{$runningNumber}";
    }

    /**
     * AJAX endpoint to generate new nomor pembayaran
     */
    public function generateNomor()
    {
        try {
            $nomorPembayaran = $this->generateNomorPembayaranSIS();
            return response()->json([
                'success' => true,
                'nomor_pembayaran' => $nomorPembayaran
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
