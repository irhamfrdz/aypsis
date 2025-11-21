<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaUangJalan;
use App\Models\PranotaUangJalan;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Models\Prospek;
use App\Models\SuratJalan;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranPranotaUangJalanController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-uang-jalan-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PembayaranPranotaUangJalan::with(['pranotaUangJalan', 'createdBy', 'updatedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by payment method
        if ($request->filled('metode')) {
            $query->byMethod($request->metode);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_pembayaran', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        // Search by payment number or reference number
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $pembayaran = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(15);

        $statuses = $this->getStatuses();
        $methods = $this->getPaymentMethods();

        return view('pembayaran-pranota-uang-jalan.index', compact('pembayaran', 'statuses', 'methods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get pranota uang jalan with optional date filtering
        $pranotaUangJalanQuery = PranotaUangJalan::query();

        // Only show unpaid pranota
        $pranotaUangJalanQuery->where('status_pembayaran', 'unpaid')
            ->whereDoesntHave('pembayaranPranotaUangJalan');

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
                'uangJalans.suratJalan.supirKaryawan',
                'uangJalans.suratJalan.tujuanPengambilanRelation',
                'uangJalans.suratJalan.tujuanPengirimanRelation'
            ])
            ->orderBy('tanggal_pranota', 'desc')
            ->get();

        // Get akun COA for bank selection
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        // Generate nomor pembayaran using SIS modul
        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-uang-jalan.create', compact('pranotaUangJalans', 'nomorPembayaran', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_uang_jalan_ids' => ['required', 'array', 'min:1'],
            'pranota_uang_jalan_ids.*' => ['exists:pranota_uang_jalans,id'],
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

            // Process each selected pranota
            $totalProspeksCreated = 0;
            $pembayaranIds = [];
            
            foreach ($validated['pranota_uang_jalan_ids'] as $index => $pranotaId) {
                $pranota = PranotaUangJalan::with(['uangJalans'])->findOrFail($pranotaId);
                
                // Prepare payment data
                $paymentData = $validated;
                $paymentData['pranota_uang_jalan_id'] = $pranotaId;
                // Use same payment number for all pranota in this transaction
                $paymentData['nomor_pembayaran'] = $nomorPembayaran;
                
                // Remove array field (not needed in payment table)
                unset($paymentData['pranota_uang_jalan_ids']);
                
                // Set status pembayaran as paid (setelah pembayaran berhasil disimpan)
                $paymentData['status_pembayaran'] = PembayaranPranotaUangJalan::STATUS_PAID;
                
                // Set created and updated by
                $paymentData['created_by'] = Auth::id();
                $paymentData['updated_by'] = Auth::id();

                // Create the payment
                $pembayaran = PembayaranPranotaUangJalan::create($paymentData);
                $pembayaranIds[] = $pembayaran->id;

                // Update pranota status to paid
                $pranota->update([
                    'status_pembayaran' => PranotaUangJalan::STATUS_PAID,
                    'updated_by' => Auth::id()
                ]);

                // Update all uang jalan in this pranota to 'lunas' status
                foreach ($pranota->uangJalans as $uangJalan) {
                    $uangJalan->update([
                        'status' => 'lunas',
                        'updated_by' => Auth::id()
                    ]);
                }

                // Create prospek from FCL/CARGO uang jalan after successful payment
                $prospeksCount = $this->createProspekFromFclUangJalan($pranotaId);
                $totalProspeksCreated += $prospeksCount;
            }

            // Catat transaksi menggunakan double-entry COA untuk semua pembayaran
            $totalPembayaran = $validated['total_tagihan_setelah_penyesuaian'] ?? $validated['total_pembayaran'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $keterangan = "Pembayaran Pranota Uang Jalan - " . $validated['nomor_pembayaran'];

            // Tentukan apakah bank di-debit atau di-kredit berdasarkan jenis transaksi
            if ($jenisTransaksi == 'Debit') {
                // Jenis Debit: Bank bertambah (Debit), Biaya Uang Jalan berkurang (Kredit)
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran], // DEBIT Bank
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran], // KREDIT Biaya
                    $validated['tanggal_pembayaran'],
                    $validated['nomor_pembayaran'],
                    'Pembayaran Pranota Uang Jalan',
                    $keterangan
                );
            } else {
                // Jenis Kredit: Biaya Uang Jalan bertambah (Debit), Bank berkurang (Kredit)
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran], // DEBIT Biaya
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran], // KREDIT Bank
                    $validated['tanggal_pembayaran'],
                    $validated['nomor_pembayaran'],
                    'Pembayaran Pranota Uang Jalan',
                    $keterangan
                );
            }

            // Log accounting entry
            Log::info('Double Entry Accounting recorded for Pembayaran Pranota Uang Jalan', [
                'nomor_pembayaran' => $validated['nomor_pembayaran'],
                'total_pembayaran' => $totalPembayaran,
                'bank' => $bankName,
                'jenis_transaksi' => $jenisTransaksi,
                'biaya_account' => 'Biaya Uang Jalan Muat',
                'double_entry_success' => $doubleEntryResult,
                'pranota_count' => count($validated['pranota_uang_jalan_ids'])
            ]);

            DB::commit();

            // Prepare success message
            $successMessage = 'Pembayaran berhasil disimpan untuk ' . count($pembayaranIds) . ' pranota uang jalan.';
            if ($totalProspeksCreated > 0) {
                $successMessage .= ' ' . $totalProspeksCreated . ' data prospek FCL/CARGO telah dibuat otomatis.';
            }

            // Redirect langsung ke index page setelah berhasil membuat pembayaran
            return redirect()->route('pembayaran-pranota-uang-jalan.index')
                           ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();

            // Log detailed error information
            Log::error('Error creating Pembayaran Pranota Uang Jalan', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'form_data' => $validated,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Provide specific error messages based on error type
            $errorMessage = 'Gagal menyimpan pembayaran. ';

            if (str_contains($e->getMessage(), 'COA tidak ditemukan')) {
                $errorMessage .= 'Akun bank atau beban uang jalan tidak ditemukan dalam Chart of Accounts.';
            } elseif (str_contains($e->getMessage(), 'double entry')) {
                $errorMessage .= 'Terjadi kesalahan dalam pencatatan akuntansi. Silakan periksa konfigurasi COA.';
            } elseif (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'Nomor pembayaran sudah ada. Silakan refresh halaman untuk mendapatkan nomor baru.';
            } elseif (str_contains($e->getMessage(), 'foreign key')) {
                $errorMessage .= 'Data pranota uang jalan yang dipilih tidak valid.';
            } else {
                $errorMessage .= 'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.';
            }

            return back()->withInput()->with('error', $errorMessage . ' Detail: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranPranotaUangJalan $pembayaranPranotaUangJalan)
    {
        $pembayaranPranotaUangJalan->load(['pranotaUangJalan', 'createdBy', 'updatedBy']);
        
        return view('pembayaran-pranota-uang-jalan.show', compact('pembayaranPranotaUangJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranPranotaUangJalan $pembayaranPranotaUangJalan)
    {
        if ($pembayaranPranotaUangJalan->isPaid() || $pembayaranPranotaUangJalan->isCancelled()) {
            return redirect()->route('pembayaran-pranota-uang-jalan.index')
                ->with('error', 'Pembayaran yang sudah lunas atau dibatalkan tidak dapat diubah.');
        }

        return view('pembayaran-pranota-uang-jalan.edit', compact('pembayaranPranotaUangJalan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranPranotaUangJalan $pembayaranPranotaUangJalan)
    {
        if ($pembayaranPranotaUangJalan->isPaid() || $pembayaranPranotaUangJalan->isCancelled()) {
            return redirect()->route('pembayaran-pranota-uang-jalan.index')
                ->with('error', 'Pembayaran yang sudah lunas atau dibatalkan tidak dapat diubah.');
        }

        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['cash', 'transfer', 'check', 'giro'])],
            'total_pembayaran' => 'required|numeric|min:0',
            'bank' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        DB::beginTransaction();

        try {
            // Calculate adjustment
            $totalTagihanSetelahPenyesuaian = $request->total_pembayaran;
            $totalTagihanPenyesuaian = $totalTagihanSetelahPenyesuaian - $pembayaranPranotaUangJalan->pranotaUangJalan->total_amount;

            $data = [
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'jenis_transaksi' => $request->jenis_transaksi,
                'total_pembayaran' => $request->total_pembayaran,
                'total_tagihan_penyesuaian' => $totalTagihanPenyesuaian,
                'total_tagihan_setelah_penyesuaian' => $totalTagihanSetelahPenyesuaian,
                'bank' => $request->bank,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ];

            // Handle file upload
            if ($request->hasFile('bukti_pembayaran')) {
                // Delete old file if exists
                if ($pembayaranPranotaUangJalan->bukti_pembayaran) {
                    Storage::disk('public')->delete($pembayaranPranotaUangJalan->bukti_pembayaran);
                }

                $file = $request->file('bukti_pembayaran');
                $filename = 'bukti_pembayaran_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
                $data['bukti_pembayaran'] = $path;
            }

            $pembayaranPranotaUangJalan->update($data);

            DB::commit();

            return redirect()->route('pembayaran-pranota-uang-jalan.index')
                ->with('success', 'Pembayaran pranota uang jalan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pembayaran pranota uang jalan: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal memperbarui pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranPranotaUangJalan $pembayaranPranotaUangJalan)
    {
        if ($pembayaranPranotaUangJalan->isPaid()) {
            return redirect()->route('pembayaran-pranota-uang-jalan.index')
                ->with('error', 'Pembayaran yang sudah lunas tidak dapat dihapus.');
        }

        DB::beginTransaction();

        try {
            // Update pranota status back to unpaid
            $pembayaranPranotaUangJalan->pranotaUangJalan->update([
                'status_pembayaran' => 'unpaid'
            ]);

            // Delete file if exists
            if ($pembayaranPranotaUangJalan->bukti_pembayaran) {
                Storage::disk('public')->delete($pembayaranPranotaUangJalan->bukti_pembayaran);
            }

            $pembayaranPranotaUangJalan->delete();

            DB::commit();

            return redirect()->route('pembayaran-pranota-uang-jalan.index')
                ->with('success', 'Pembayaran pranota uang jalan berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembayaran pranota uang jalan: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Get available statuses
     */
    private function getStatuses()
    {
        return [
            PembayaranPranotaUangJalan::STATUS_PENDING => 'Menunggu',
            PembayaranPranotaUangJalan::STATUS_PAID => 'Lunas',
            PembayaranPranotaUangJalan::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    /**
     * Get available payment methods
     */
    private function getPaymentMethods()
    {
        return [
            PembayaranPranotaUangJalan::METHOD_CASH => 'Tunai',
            PembayaranPranotaUangJalan::METHOD_TRANSFER => 'Transfer',
            PembayaranPranotaUangJalan::METHOD_CHECK => 'Cek',
            PembayaranPranotaUangJalan::METHOD_GIRO => 'Giro',
        ];
    }

    /**
     * Create prospek from FCL/CARGO uang jalan after payment.
     * Data yang masuk: tanggal masuk prospek, nama supir, barang, pengirim, ukuran, tujuan
     * Data diambil dari uang jalan yang terkait dengan surat jalan FCL/CARGO
     *
     * @return int Number of prospeks created
     */
    private function createProspekFromFclUangJalan($pranotaId)
    {
        $prospeksCreated = 0;

        try {
            // Get pranota with related uang jalan
            $pranota = PranotaUangJalan::with(['uangJalans.suratJalan'])->find($pranotaId);

            if (!$pranota || !$pranota->uangJalans) {
                return $prospeksCreated;
            }

            // Loop through all uang jalan in this pranota
            foreach ($pranota->uangJalans as $uangJalan) {
                // Check if uang jalan has related surat jalan
                if (!$uangJalan->suratJalan) {
                    continue;
                }

                $suratJalan = $uangJalan->suratJalan;

                // Only process FCL and CARGO type surat jalan
                $tipeKontainer = strtoupper($suratJalan->tipe_kontainer ?? '');
                if ($tipeKontainer !== 'FCL' && $tipeKontainer !== 'CARGO') {
                    continue;
                }

                // Get jumlah kontainer untuk surat jalan ini
                $jumlahKontainer = $suratJalan->jumlah_kontainer ?? 1;
                
                // Create prospek berdasarkan jumlah kontainer
                for ($i = 1; $i <= $jumlahKontainer; $i++) {
                    // Parse nomor kontainer dan seal jika sudah ada dari checkpoint
                    $nomorKontainerArray = [];
                    $noSealArray = [];
                    
                    if (!empty($suratJalan->no_kontainer)) {
                        $nomorKontainerArray = array_map('trim', explode(',', $suratJalan->no_kontainer));
                    }
                    
                    if (!empty($suratJalan->no_seal)) {
                        $noSealArray = array_map('trim', explode(',', $suratJalan->no_seal));
                    }
                    
                    // Ambil nomor kontainer dan seal untuk kontainer ke-i (jika ada)
                    $nomorKontainerIni = isset($nomorKontainerArray[$i-1]) ? $nomorKontainerArray[$i-1] : null;
                    $noSealIni = isset($noSealArray[$i-1]) ? $noSealArray[$i-1] : null;

                    // Prepare prospek data from uang jalan dan surat jalan
                    $prospekData = [
                        'tanggal' => now(), // Tanggal masuk prospek adalah hari ini (saat pembayaran)
                        'nama_supir' => $uangJalan->supir ?? $suratJalan->supir ?? null,
                        'barang' => $suratJalan->jenis_barang ?? null,
                        'pt_pengirim' => $suratJalan->pengirim ?? null,
                        'ukuran' => $suratJalan->size ?? null,
                        'tipe' => $suratJalan->tipe_kontainer ?? null, // FCL, LCL, dll
                        'no_surat_jalan' => $suratJalan->no_surat_jalan ?? null,
                        'surat_jalan_id' => $suratJalan->id,
                        'nomor_kontainer' => $nomorKontainerIni ?: null,
                        'no_seal' => $noSealIni,
                        'tujuan_pengiriman' => $uangJalan->tujuan ?? $suratJalan->tujuan_pengiriman ?? null,
                        'nama_kapal' => null, // Belum ada
                        'keterangan' => 'Auto generated dari Uang Jalan: ' . ($uangJalan->dari ?? '') . ' - ' . ($uangJalan->tujuan ?? '') . 
                                      ' | Surat Jalan: ' . ($suratJalan->no_surat_jalan ?? '-') . 
                                      ' | Pranota: ' . ($pranota->nomor_pranota ?? '-') . 
                                      ($jumlahKontainer > 1 ? " | Kontainer #$i dari $jumlahKontainer" : ''),
                        'status' => Prospek::STATUS_AKTIF,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ];

                    // Create prospek untuk kontainer ke-i
                    $prospek = Prospek::create($prospekData);
                    $prospeksCreated++;

                    Log::info('Prospek created from FCL/CARGO Uang Jalan', [
                        'prospek_id' => $prospek->id,
                        'uang_jalan_id' => $uangJalan->id,
                        'surat_jalan_id' => $suratJalan->id,
                        'surat_jalan_no' => $suratJalan->no_surat_jalan,
                        'kontainer_number' => $i,
                        'total_kontainer' => $jumlahKontainer,
                        'nomor_kontainer' => $nomorKontainerIni,
                        'no_seal' => $noSealIni,
                        'pranota_id' => $pranotaId,
                        'pranota_no' => $pranota->nomor_pranota,
                        'tipe_kontainer' => $suratJalan->tipe_kontainer,
                        'supir' => $uangJalan->supir ?? $suratJalan->supir,
                        'pengirim' => $suratJalan->pengirim,
                        'rute' => ($uangJalan->dari ?? '') . ' - ' . ($uangJalan->tujuan ?? '')
                    ]);
                }
            }

            if ($prospeksCreated > 0) {
                Log::info('Total prospek created from uang jalan payment', [
                    'pranota_id' => $pranotaId,
                    'total_prospeks' => $prospeksCreated
                ]);
            }

        } catch (\Exception $e) {
            // Log error but don't fail the payment process
            Log::error('Error creating prospek from FCL/CARGO uang jalan', [
                'pranota_id' => $pranotaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $prospeksCreated;
    }

    /**
     * Generate nomor pembayaran menggunakan modul SIS
     * Format: SIS-[MM]-[YY]-[NNNNNN]
     */
    private function generateNomorPembayaranSIS()
    {
        // Get or create SIS modul
        $modulSis = NomorTerakhir::firstOrCreate(
            ['modul' => 'SIS'],
            [
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor Pembayaran Pranota Uang Jalan'
            ]
        );

        // Get current date
        $now = now();
        $bulan = $now->format('m'); // 2 digit month
        $tahun = $now->format('y'); // 2 digit year
        
        // Get next running number
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);
        
        // Format: SIS-MM-YY-NNNNNN
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
            Log::error('Error generating nomor pembayaran: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}