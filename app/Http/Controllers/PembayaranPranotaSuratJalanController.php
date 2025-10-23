<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaSuratJalan;
use App\Models\PranotaSuratJalan;
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

class PembayaranPranotaSuratJalanController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-surat-jalan-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-surat-jalan-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-surat-jalan-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-surat-jalan-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PembayaranPranotaSuratJalan::with(['pranotaSuratJalan', 'creator', 'updater']);

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
            $query->byDateRange($request->tanggal_dari, $request->tanggal_sampai);
        }

        // Search by payment number or reference number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                  ->orWhere('nomor_referensi', 'like', "%{$search}%")
                  ->orWhereHas('pranotaSuratJalan', function ($sq) use ($search) {
                      $sq->where('nomor_pranota', 'like', "%{$search}%");
                  });
            });
        }

        $pembayaran = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(15);

        $statuses = PembayaranPranotaSuratJalan::getStatuses();
        $methods = PembayaranPranotaSuratJalan::getPaymentMethods();

        return view('pembayaran-pranota-surat-jalan.index', compact('pembayaran', 'statuses', 'methods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get pranota surat jalan with optional date filtering
        $pranotaSuratJalanQuery = PranotaSuratJalan::query();

        // Only show unpaid or partially paid pranota
        $pranotaSuratJalanQuery->whereIn('status_pembayaran', ['unpaid', 'partial']);

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaSuratJalanQuery->whereBetween('tanggal_pranota', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // If pranota_id is provided, filter for specific pranota
        if ($request->filled('pranota_id')) {
            $pranotaSuratJalanQuery->where('id', $request->pranota_id);
        }

        $pranotaSuratJalan = $pranotaSuratJalanQuery
            ->with(['suratJalans' => function($query) {
                $query->select('surat_jalans.*');
            }])
            ->orderBy('tanggal_pranota', 'desc')
            ->get();

        // Get akun COA for bank selection (same as pranota supir)
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        $statuses = PembayaranPranotaSuratJalan::getStatuses();
        $methods = PembayaranPranotaSuratJalan::getPaymentMethods();

        return view('pembayaran-pranota-surat-jalan.create', compact('pranotaSuratJalan', 'statuses', 'methods', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_surat_jalan_ids' => ['required', 'array', 'min:1'],
            'pranota_surat_jalan_ids.*' => ['exists:pranota_surat_jalans,id'],
            'nomor_cetakan' => ['nullable', 'integer', 'min:1', 'max:9'],
            'tanggal_pembayaran' => ['required', 'date'],
            'bank' => ['nullable', 'string', 'max:255'],
            'jenis_transaksi' => ['nullable', 'in:Debit,Kredit'],
            'total_pembayaran' => ['required', 'numeric', 'min:0'],
            'total_tagihan_penyesuaian' => ['nullable', 'numeric'],
            'total_tagihan_setelah_penyesuaian' => ['nullable', 'numeric'],
            'alasan_penyesuaian' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor pembayaran otomatis
            $bankCode = '000'; // Default
            if ($validated['bank']) {
                // Extract bank code dari akun COA yang dipilih
                $selectedBank = Coa::where('nama_akun', $validated['bank'])->first();
                if ($selectedBank && $selectedBank->kode_nomor) {
                    $bankCode = $selectedBank->kode_nomor;
                }
            }

            $nomorCetakan = $validated['nomor_cetakan'] ?? 1;
            $nomorPembayaran = NomorTerakhir::generateNomorPembayaranCustom($bankCode, $nomorCetakan);

            // Add generated nomor_pembayaran to validated data
            $validated['nomor_pembayaran'] = $nomorPembayaran;

            // Handle file upload
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = 'pembayaran_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('pembayaran/pranota-surat-jalan', $filename, 'public');
                $validated['bukti_pembayaran'] = $path;
            }

            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            // Create separate payment record for each selected pranota
            $pembayaranIds = [];
            $totalProspeksCreated = 0;

            foreach ($validated['pranota_surat_jalan_ids'] as $index => $pranotaId) {
                $paymentData = $validated;
                $paymentData['pranota_surat_jalan_id'] = $pranotaId;

                // Modify nomor pembayaran for multiple entries
                if ($index > 0) {
                    $paymentData['nomor_pembayaran'] = $validated['nomor_pembayaran'] . '-' . ($index + 1);
                }

                // Remove the array field
                unset($paymentData['pranota_surat_jalan_ids']);

                // Simple logic: if payment is created, status is 'paid'
                $paymentData['status_pembayaran'] = 'paid';

                $pembayaran = PembayaranPranotaSuratJalan::create($paymentData);
                $pembayaranIds[] = $pembayaran->id;

                // Update pranota surat jalan payment status if method exists
                if (method_exists($this, 'updatePranotaPaymentStatus')) {
                    $this->updatePranotaPaymentStatus($pranotaId);
                }

                // Create prospek from FCL surat jalan after successful payment
                $prospeksCount = $this->createProspekFromFclSuratJalan($pranotaId);
                $totalProspeksCreated += $prospeksCount;
            }

            // Catat transaksi menggunakan double-entry COA untuk semua pembayaran
            $totalPembayaran = $validated['total_tagihan_setelah_penyesuaian'] ?? $validated['total_pembayaran'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $keterangan = "Pembayaran Pranota Surat Jalan - " . $validated['nomor_pembayaran'];

            // Tentukan apakah bank di-debit atau di-kredit berdasarkan jenis transaksi
            if ($jenisTransaksi == 'Debit') {
                // Jenis Debit: Bank bertambah (Debit), Biaya Uang Jalan Muat berkurang (Kredit)
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran], // DEBIT Bank
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran], // KREDIT Biaya
                    $validated['tanggal_pembayaran'],
                    $validated['nomor_pembayaran'],
                    'Pembayaran Pranota Surat Jalan',
                    $keterangan
                );
            } else {
                // Jenis Kredit: Biaya Uang Jalan Muat bertambah (Debit), Bank berkurang (Kredit)
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => $totalPembayaran], // DEBIT Biaya
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran], // KREDIT Bank
                    $validated['tanggal_pembayaran'],
                    $validated['nomor_pembayaran'],
                    'Pembayaran Pranota Surat Jalan',
                    $keterangan
                );
            }

            // Log accounting entry
            Log::info('Double Entry Accounting recorded for Pembayaran Pranota Surat Jalan', [
                'nomor_pembayaran' => $validated['nomor_pembayaran'],
                'total_pembayaran' => $totalPembayaran,
                'bank' => $bankName,
                'jenis_transaksi' => $jenisTransaksi,
                'biaya_account' => 'Biaya Uang Jalan Muat',
                'double_entry_success' => $doubleEntryResult,
                'pranota_count' => count($pembayaranIds)
            ]);

            DB::commit();

            // Prepare success message
            $successMessage = 'Pembayaran berhasil disimpan untuk ' . count($pembayaranIds) . ' pranota surat jalan.';
            if ($totalProspeksCreated > 0) {
                $successMessage .= ' ' . $totalProspeksCreated . ' data prospek FCL telah dibuat otomatis.';
            }

            // Redirect to first payment record or index page
            $firstPembayaranId = $pembayaranIds[0] ?? null;
            if ($firstPembayaranId) {
                return redirect()->route('pembayaran-pranota-surat-jalan.show', $firstPembayaranId)
                               ->with('success', $successMessage);
            } else {
                return redirect()->route('pembayaran-pranota-surat-jalan.index')
                               ->with('success', $successMessage);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if exists
            if (isset($validated['bukti_pembayaran'])) {
                Storage::disk('public')->delete($validated['bukti_pembayaran']);
            }

            // Log detailed error information
            Log::error('Error creating Pembayaran Pranota Surat Jalan', [
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
                $errorMessage .= 'Akun bank yang dipilih tidak ditemukan dalam Chart of Accounts.';
            } elseif (str_contains($e->getMessage(), 'double entry')) {
                $errorMessage .= 'Terjadi kesalahan dalam pencatatan akuntansi. Silakan periksa konfigurasi COA.';
            } elseif (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'Nomor pembayaran sudah ada. Silakan refresh halaman untuk mendapatkan nomor baru.';
            } elseif (str_contains($e->getMessage(), 'foreign key')) {
                $errorMessage .= 'Data pranota surat jalan yang dipilih tidak valid.';
            } else {
                $errorMessage .= 'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.';
            }

            return back()->withInput()->with('error', $errorMessage . ' Detail: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranPranotaSuratJalan $pembayaranPranotaSuratJalan)
    {
        $pembayaranPranotaSuratJalan->load(['pranotaSuratJalan', 'creator', 'updater']);

        return view('pembayaran-pranota-surat-jalan.show', compact('pembayaranPranotaSuratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranPranotaSuratJalan $pembayaranPranotaSuratJalan)
    {
        // Don't allow editing paid or cancelled payments
        if (in_array($pembayaranPranotaSuratJalan->status_pembayaran, ['paid', 'cancelled'])) {
            return redirect()->route('pembayaran-pranota-surat-jalan.show', $pembayaranPranotaSuratJalan->id)
                           ->with('error', 'Pembayaran yang sudah lunas atau dibatalkan tidak dapat diedit.');
        }

        $pranotaSuratJalan = PranotaSuratJalan::whereIn('status_pembayaran', ['unpaid', 'partial'])
                                            ->orWhere('id', $pembayaranPranotaSuratJalan->pranota_surat_jalan_id)
                                            ->get();

        $statuses = PembayaranPranotaSuratJalan::getStatuses();
        $methods = PembayaranPranotaSuratJalan::getPaymentMethods();

        return view('pembayaran-pranota-surat-jalan.edit', compact('pembayaranPranotaSuratJalan', 'pranotaSuratJalan', 'statuses', 'methods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranPranotaSuratJalan $pembayaranPranotaSuratJalan)
    {
        // Don't allow updating paid or cancelled payments
        if (in_array($pembayaranPranotaSuratJalan->status_pembayaran, ['paid', 'cancelled'])) {
            return redirect()->route('pembayaran-pranota-surat-jalan.show', $pembayaranPranotaSuratJalan->id)
                           ->with('error', 'Pembayaran yang sudah lunas atau dibatalkan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'pranota_surat_jalan_id' => ['required', 'exists:pranota_surat_jalans,id'],
            'nomor_pembayaran' => ['required', 'string', 'max:255', 'unique:pembayaran_pranota_surat_jalan,nomor_pembayaran,' . $pembayaranPranotaSuratJalan->id],
            'tanggal_pembayaran' => ['required', 'date'],
            'bank' => ['nullable', 'string', 'max:255'],
            'jenis_transaksi' => ['nullable', 'in:Debit,Kredit'],
            'total_pembayaran' => ['required', 'numeric', 'min:0'],
            'total_tagihan_penyesuaian' => ['nullable', 'numeric'],
            'total_tagihan_setelah_penyesuaian' => ['nullable', 'numeric'],
            'alasan_penyesuaian' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
            'status_pembayaran' => ['required', Rule::in(array_keys(PembayaranPranotaSuratJalan::getStatuses()))],
            'bukti_pembayaran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        try {
            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('bukti_pembayaran')) {
                // Delete old file if exists
                if ($pembayaranPranotaSuratJalan->bukti_pembayaran) {
                    Storage::disk('public')->delete($pembayaranPranotaSuratJalan->bukti_pembayaran);
                }

                $file = $request->file('bukti_pembayaran');
                $filename = 'pembayaran_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('pembayaran/pranota-surat-jalan', $filename, 'public');
                $validated['bukti_pembayaran'] = $path;
            }

            $validated['updated_by'] = Auth::id();

            $oldPranotaId = $pembayaranPranotaSuratJalan->pranota_surat_jalan_id;
            $pembayaranPranotaSuratJalan->update($validated);

            // Update payment status for both old and new pranota if different
            $this->updatePranotaPaymentStatus($oldPranotaId);
            if ($oldPranotaId != $validated['pranota_surat_jalan_id']) {
                $this->updatePranotaPaymentStatus($validated['pranota_surat_jalan_id']);
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-surat-jalan.show', $pembayaranPranotaSuratJalan->id)
                           ->with('success', 'Pembayaran berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranPranotaSuratJalan $pembayaranPranotaSuratJalan)
    {
        // Don't allow deleting paid payments
        if ($pembayaranPranotaSuratJalan->status_pembayaran === 'paid') {
            return redirect()->route('pembayaran-pranota-surat-jalan.index')
                           ->with('error', 'Pembayaran yang sudah lunas tidak dapat dihapus.');
        }

        try {
            DB::beginTransaction();

            $pranotaId = $pembayaranPranotaSuratJalan->pranota_surat_jalan_id;

            // Delete file if exists
            if ($pembayaranPranotaSuratJalan->bukti_pembayaran) {
                Storage::disk('public')->delete($pembayaranPranotaSuratJalan->bukti_pembayaran);
            }

            $pembayaranPranotaSuratJalan->delete();

            // Update pranota payment status
            $this->updatePranotaPaymentStatus($pranotaId);

            DB::commit();

            return redirect()->route('pembayaran-pranota-surat-jalan.index')
                           ->with('success', 'Pembayaran berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Update pranota surat jalan payment status based on payments.
     */
    private function updatePranotaPaymentStatus($pranotaId)
    {
        $pranota = PranotaSuratJalan::find($pranotaId);
        if (!$pranota) return;

        // Simple logic: if there's any non-cancelled payment, status is 'paid'
        $hasPayment = PembayaranPranotaSuratJalan::where('pranota_surat_jalan_id', $pranotaId)
                                                ->whereNotIn('status_pembayaran', ['cancelled'])
                                                ->exists();

        $status = $hasPayment ? 'paid' : 'unpaid';
        $pranota->update(['status_pembayaran' => $status]);

        // Also update all related surat jalan status
        $this->updateSuratJalanPaymentStatus($pranotaId, $status);
    }

    /**
     * Update surat jalan payment status for all surat jalan in a pranota.
     */
    private function updateSuratJalanPaymentStatus($pranotaId, $paymentStatus)
    {
        // Get all surat jalan IDs related to this pranota
        $suratJalanIds = DB::table('pranota_surat_jalan_items')
                          ->where('pranota_surat_jalan_id', $pranotaId)
                          ->pluck('surat_jalan_id');

        if ($suratJalanIds->count() > 0) {
            // Convert payment status to surat jalan status format
            $suratJalanStatus = $paymentStatus === 'paid' ? 'sudah_dibayar' : 'belum_dibayar';

            // Update all related surat jalan
            DB::table('surat_jalans')
              ->whereIn('id', $suratJalanIds)
              ->update([
                  'status_pembayaran' => $suratJalanStatus,
                  'updated_at' => now()
              ]);

            Log::info('Updated surat jalan payment status', [
                'pranota_id' => $pranotaId,
                'surat_jalan_ids' => $suratJalanIds->toArray(),
                'new_status' => $suratJalanStatus,
                'payment_status' => $paymentStatus
            ]);
        }
    }

    /**
     * Generate payment number.
     */
    public function generatePaymentNumber()
    {
        $prefix = 'PAY-SJ-';
        $date = now()->format('Ymd');
        $lastPayment = PembayaranPranotaSuratJalan::where('nomor_pembayaran', 'like', $prefix . $date . '%')
                                                 ->orderBy('nomor_pembayaran', 'desc')
                                                 ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->nomor_pembayaran, -4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create prospek from FCL surat jalan after payment.
     * Data yang masuk: tanggal masuk prospek, nama supir, barang, pengirim, ukuran, tujuan
     * Nomor kontainer dan nomor seal belum ada karena belum masuk checkpoint
     *
     * @return int Number of prospeks created
     */
    private function createProspekFromFclSuratJalan($pranotaId)
    {
        $prospeksCreated = 0;

        try {
            // Get pranota with related surat jalan
            $pranota = PranotaSuratJalan::with('suratJalans')->find($pranotaId);

            if (!$pranota || !$pranota->suratJalans) {
                return $prospeksCreated;
            }

            // Loop through all surat jalan in this pranota
            foreach ($pranota->suratJalans as $suratJalan) {
                // Only process FCL type surat jalan
                if (strtoupper($suratJalan->tipe_kontainer) !== 'FCL') {
                    continue;
                }

                // Prepare prospek data from surat jalan
                $prospekData = [
                    'tanggal' => now(), // Tanggal masuk prospek adalah hari ini (saat pembayaran)
                    'nama_supir' => $suratJalan->supir ?? null,
                    'barang' => $suratJalan->jenis_barang ?? null,
                    'pt_pengirim' => $suratJalan->pengirim ?? null,
                    'ukuran' => $suratJalan->size ?? null,
                    'tipe' => $suratJalan->tipe_kontainer ?? null, // FCL, LCL, dll
                    'nomor_kontainer' => null, // Belum ada karena belum masuk checkpoint
                    'no_seal' => null, // Belum ada karena belum masuk checkpoint
                    'tujuan_pengiriman' => $suratJalan->tujuan_pengiriman ?? null,
                    'nama_kapal' => null, // Belum ada
                    'keterangan' => 'Auto generated dari Surat Jalan: ' . ($suratJalan->no_surat_jalan ?? '-') . ' | Pranota: ' . ($pranota->nomor_pranota ?? '-'),
                    'status' => Prospek::STATUS_AKTIF,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id()
                ];

                // Create prospek
                $prospek = Prospek::create($prospekData);
                $prospeksCreated++;

                Log::info('Prospek created from FCL Surat Jalan', [
                    'prospek_id' => $prospek->id,
                    'surat_jalan_id' => $suratJalan->id,
                    'surat_jalan_no' => $suratJalan->no_surat_jalan,
                    'pranota_id' => $pranotaId,
                    'pranota_no' => $pranota->nomor_pranota,
                    'tipe_kontainer' => $suratJalan->tipe_kontainer,
                    'supir' => $suratJalan->supir,
                    'pengirim' => $suratJalan->pengirim
                ]);
            }

            if ($prospeksCreated > 0) {
                Log::info('Total prospek created from payment', [
                    'pranota_id' => $pranotaId,
                    'total_prospeks' => $prospeksCreated
                ]);
            }

        } catch (\Exception $e) {
            // Log error but don't fail the payment process
            Log::error('Error creating prospek from FCL surat jalan', [
                'pranota_id' => $pranotaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $prospeksCreated;
    }
}
