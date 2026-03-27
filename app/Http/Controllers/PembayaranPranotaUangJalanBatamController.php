<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaUangJalanBatam;
use App\Models\PranotaUangJalanBatam;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Models\Prospek;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranPranotaUangJalanBatamController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-uang-jalan-batam-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-batam-create')->only(['create', 'store', 'generateNomor']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-batam-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-batam-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PranotaUangJalanBatam::with([
            'uangJalanBatams.suratJalanBatam.supirKaryawan',
            'creator', 
            'updater'
        ]);

        // Filter by status pembayaran
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_pranota', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        // Search by pranota number or supir name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhereHas('uangJalanBatams.suratJalanBatam', function($sq) use ($search) {
                      $sq->where('supir', 'like', "%{$search}%");
                  });
            });
        }

        $pranotaList = $query->orderBy('tanggal_pranota', 'desc')->paginate(15);

        $statuses = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            'cancelled' => 'Dibatalkan'
        ];

        return view('pembayaran-pranota-uang-jalan-batam.index', compact('pranotaList', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $pranotaUangJalanQuery = PranotaUangJalanBatam::query();

        // Only show unpaid pranota that don't have payment yet
        $pranotaUangJalanQuery->where('status_pembayaran', 'unpaid');

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaUangJalanQuery->whereBetween('tanggal_pranota', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // If pranota_id is provided, filter for specific pranota
        if ($request->filled('pranota_id')) {
            $pranotaUangJalanQuery->where('id', $request->pranota_id);
        }

        $pranotaUangJalans = $pranotaUangJalanQuery
            ->with([
                'uangJalanBatams.suratJalanBatam.supirKaryawan',
                'uangJalanBatams.suratJalanBatam.tujuanPengambilanRelation',
                'uangJalanBatams.suratJalanBatam.tujuanPengirimanRelation'
            ])
            ->orderBy('tanggal_pranota', 'desc')
            ->get();

        // Get akun COA for bank selection
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        // Generate nomor pembayaran using SIS prefix (or custom for Batam if needed)
        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-uang-jalan-batam.create', compact('pranotaUangJalans', 'nomorPembayaran', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_uang_jalan_ids' => ['required', 'array', 'min:1'],
            'pranota_uang_jalan_ids.*' => ['exists:pranota_uang_jalan_batams,id'],
            'nomor_pembayaran' => 'nullable|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['Debit', 'Kredit', 'cash', 'transfer', 'check', 'giro'])],
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
            // Generate new payment number and increment
            $nomorPembayaran = $this->generateNomorPembayaranSIS();
            
            // Increment nomor_terakhir for SIS modul
            DB::table('nomor_terakhir')->where('modul', 'SIS')->increment('nomor_terakhir');

            // Prepare payment data
            $paymentData = $validated;
            unset($paymentData['pranota_uang_jalan_ids']);
            
            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = 'paid';
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            // Create ONE payment record
            $pembayaran = PembayaranPranotaUangJalanBatam::create($paymentData);

            // Process each selected pranota
            foreach ($validated['pranota_uang_jalan_ids'] as $pranotaId) {
                $pranota = PranotaUangJalanBatam::with(['uangJalanBatams'])->findOrFail($pranotaId);
                
                // Attach to payment
                $pembayaran->pranotaUangJalanBatams()->attach($pranotaId, [
                    'subtotal' => $pranota->total_for_payment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update pranota status
                $pranota->update([
                    'status_pembayaran' => 'paid',
                    'updated_by' => Auth::id()
                ]);

                // Update all uang jalan batam in this pranota to 'lunas'
                foreach ($pranota->uangJalanBatams as $uj) {
                    $uj->update([
                        'status' => 'lunas',
                        'updated_by' => Auth::id()
                    ]);
                    
                    if ($uj->suratJalanBatam) {
                        $uj->suratJalanBatam->update([
                            'status_pembayaran_uang_jalan' => 'dibayar',
                            'status' => 'belum masuk checkpoint',
                            'updated_by' => Auth::id()
                        ]);
                    }
                }
            }

            // Record COA entry
            $totalPembayaran = $validated['total_tagihan_setelah_penyesuaian'] ?? $validated['total_pembayaran'];
            $bankName = $validated['bank'];
            $keterangan = "Pembayaran Pranota Uang Jalan Batam - " . $nomorPembayaran;

            // Simplified COA recording based on original controller logic
            $this->coaTransactionService->recordDoubleEntry(
                ['nama_akun' => ($validated['jenis_transaksi'] == 'Debit' ? $bankName : 'Biaya Uang Jalan Muat'), 'jumlah' => $totalPembayaran],
                ['nama_akun' => ($validated['jenis_transaksi'] == 'Debit' ? 'Biaya Uang Jalan Muat' : $bankName), 'jumlah' => $totalPembayaran],
                $validated['tanggal_pembayaran'],
                $nomorPembayaran,
                'Pembayaran Pranota Uang Jalan Batam',
                $keterangan
            );

            DB::commit();

            return redirect()->route('pembayaran-pranota-uang-jalan-batam.index')
                           ->with('success', 'Pembayaran ' . $nomorPembayaran . ' berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Uang Jalan Batam: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pembayaranPranotaUangJalanBatam = PembayaranPranotaUangJalanBatam::with(['pranotaUangJalanBatams', 'createdBy', 'updatedBy'])->findOrFail($id);
        
        return view('pembayaran-pranota-uang-jalan-batam.show', compact('pembayaranPranotaUangJalanBatam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pembayaranPranotaUangJalanBatam = PembayaranPranotaUangJalanBatam::with([
            'pranotaUangJalanBatams.uangJalanBatams.suratJalanBatam.supirKaryawan',
            'pranotaUangJalanBatams.uangJalanBatams.suratJalanBatam.tujuanPengambilanRelation',
            'pranotaUangJalanBatams.uangJalanBatams.suratJalanBatam.tujuanPengirimanRelation'
        ])->findOrFail($id);

        if ($pembayaranPranotaUangJalanBatam->isCancelled()) {
            return redirect()->route('pembayaran-pranota-uang-jalan-batam.index')
                ->with('error', 'Pembayaran yang dibatalkan tidak dapat diubah.');
        }

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        return view('pembayaran-pranota-uang-jalan-batam.edit', compact('pembayaranPranotaUangJalanBatam', 'akunCoa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranPranotaUangJalanBatam::findOrFail($id);

        if ($pembayaran->isCancelled()) {
            return redirect()->route('pembayaran-pranota-uang-jalan-batam.index')
                ->with('error', 'Pembayaran yang dibatalkan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'nullable|date',
        ]);

        try {
            $pembayaran->update([
                'nomor_accurate' => $validated['nomor_accurate'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'] ?: $pembayaran->tanggal_pembayaran,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('pembayaran-pranota-uang-jalan-batam.index')
                ->with('success', 'Nomor Accurate berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating Pembayaran Pranota Uang Jalan Batam: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pembayaran = PembayaranPranotaUangJalanBatam::findOrFail($id);

        if ($pembayaran->isPaid()) {
            return redirect()->route('pembayaran-pranota-uang-jalan-batam.index')
                ->with('error', 'Pembayaran yang sudah lunas tidak dapat dihapus.');
        }

        DB::beginTransaction();

        try {
            // Revert pranota status
            foreach($pembayaran->pranotaUangJalanBatams as $pranota) {
                $pranota->update(['status_pembayaran' => 'unpaid']);
            }

            if ($pembayaran->bukti_pembayaran) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }

            $pembayaran->delete();

            DB::commit();

            return redirect()->route('pembayaran-pranota-uang-jalan-batam.index')
                ->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pembayaran.');
        }
    }

    /**
     * Generate nomor pembayaran menggunakan modul SIS
     * Format: SIS-[BT]-[MM]-[YY]-[NNNNNN] (BT for Batam)
     */
    private function generateNomorPembayaranSIS()
    {
        $modulSis = NomorTerakhir::firstOrCreate(
            ['modul' => 'SIS'],
            ['nomor_terakhir' => 0, 'keterangan' => 'Nomor Pembayaran']
        );

        $now = now();
        $bulan = $now->format('m');
        $tahun = $now->format('y');
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);
        
        return "SIS-BT-{$bulan}-{$tahun}-{$runningNumber}";
    }
}
