<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaOb;
use App\Models\PranotaOb;
use App\Models\Coa;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PembayaranPranotaObController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }

    public function index()
    {
        // Get all pembayaran_pranota_ob
        $pembayaranList = PembayaranPranotaOb::orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembayaran-pranota-ob.index', compact('pembayaranList'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranPranotaOb::with('pembayaranOb')->findOrFail($id);

        return view('pembayaran-pranota-ob.show', compact('pembayaran'));
    }

    /**
     * Show page to select criteria (kapal, voyage, dp)
     */
    public function selectCriteria()
    {
        // Check permission manually to provide better error message
        if (!Gate::allows('pembayaran-pranota-ob-create')) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota OB. Silakan hubungi administrator.');
        }

        // Get distinct kapal and voyage from unpaid pranota OB
        $kapalList = PranotaOb::where('status', 'unpaid')
            ->distinct()
            ->pluck('nama_kapal')
            ->filter()
            ->sort()
            ->values();

        $voyageList = PranotaOb::where('status', 'unpaid')
            ->distinct()
            ->pluck('no_voyage')
            ->filter()
            ->sort()
            ->values();

        // Get DP list from pembayaran_obs where dp_amount > 0
        $dpList = \App\Models\PembayaranOb::where('dp_amount', '>', 0)
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();

        return view('pembayaran-pranota-ob.select-criteria', compact('kapalList', 'voyageList', 'dpList'));
    }

    /**
     * Show form to select pranota OB for payment
     */
    public function create(Request $request)
    {
        // Check permission manually to provide better error message
        if (!Gate::allows('pembayaran-pranota-ob-create')) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota OB. Silakan hubungi administrator.');
        }

        // If no criteria provided, redirect to select criteria page
        // Note: DP is optional, only kapal and voyage are required
        if (!$request->has('kapal') || !$request->has('voyage')) {
            return redirect()->route('pembayaran-pranota-ob.select-criteria')
                ->with('error', 'Silakan pilih kapal dan voyage terlebih dahulu.');
        }

        // Clear any old validation errors from session for fresh form load
        if ($request->isMethod('get')) {
            session()->forget('errors');
        }

        // Get the selected DP
        $selectedDp = null;
        $dpSupirData = [];
        if ($request->filled('dp')) {
            $selectedDp = \App\Models\PembayaranOb::find($request->dp);
            
            // Build dpSupirData array dari jumlah_per_supir
            if ($selectedDp && $selectedDp->jumlah_per_supir) {
                $jumlahPerSupir = is_array($selectedDp->jumlah_per_supir) ? $selectedDp->jumlah_per_supir : json_decode($selectedDp->jumlah_per_supir, true);
                
                if (is_array($jumlahPerSupir)) {
                    foreach ($jumlahPerSupir as $supirId => $jumlah) {
                        // Get supir name
                        $supir = \App\Models\Karyawan::find($supirId);
                        if ($supir) {
                            $namaSupir = $supir->nama_panggilan ? $supir->nama_panggilan : $supir->nama_lengkap;
                            $dpSupirData[strtoupper(trim($namaSupir))] = floatval($jumlah);
                        }
                    }
                }
            }
        }

        // Get pranota OB filtered by kapal, voyage
        $query = PranotaOb::where('status', 'unpaid');

        if ($request->filled('kapal')) {
            $query->where('nama_kapal', $request->kapal);
        }

        if ($request->filled('voyage')) {
            $query->where('no_voyage', $request->voyage);
        }

        $pranotaList = $query->orderBy('created_at', 'desc')->get();

        // Check if any pranota found
        if ($pranotaList->isEmpty()) {
            return redirect()->route('pembayaran-pranota-ob.select-criteria')
                ->with('error', 'Tidak ada pranota OB yang sesuai dengan kriteria yang dipilih.');
        }

        // Get all COA accounts for Akun Biaya
        $akunBiaya = Coa::orderBy('kode_nomor')->get();

        // Get Bank/Kas accounts only, sorted by account number
        $akunBank = Coa::where(function($query) {
                $query->where('tipe_akun', 'Kas/Bank')
                      ->orWhere('tipe_akun', 'Bank/Kas')
                      ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                      ->orWhere('tipe_akun', 'LIKE', '%Bank%');
            })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        return view('pembayaran-pranota-ob.create', compact('pranotaList', 'akunBank', 'akunBiaya', 'selectedDp', 'dpSupirData'));
    }

    /**
     * Store payment for pranota OB
     */
    public function store(Request $request)
    {
        // Check permission manually to provide better error message
        if (!Gate::allows('pembayaran-pranota-ob-create')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota OB. Silakan hubungi administrator.');
        }

        try {
            DB::beginTransaction();
            Log::info('Starting pembayaran pranota OB store', $request->all());

            $request->validate([
                'nomor_pembayaran' => 'required|string',
                'nomor_accurate' => 'nullable|string|max:255',
                'debit_kredit' => 'required|in:debit,credit',
                'akun_coa_id' => 'required|exists:akun_coa,id',
                'akun_bank_id' => 'required|exists:akun_coa,id',
                'tanggal_kas' => 'required|date',
                'pranota_ids' => 'required|array|min:1',
                'pranota_ids.*' => 'exists:pranota_obs,id',
                'total_tagihan_penyesuaian' => 'nullable|numeric',
                'alasan_penyesuaian' => 'nullable|string',
                'keterangan' => 'nullable|string',
                'kapal' => 'nullable|string',
                'voyage' => 'nullable|string',
                'dp_id' => 'nullable|exists:pembayaran_obs,id',
                'breakdown_supir' => 'nullable|json'
            ]);

            $pranotaIds = $request->input('pranota_ids');
            $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', 0));

            // Get and validate pranota records
            $pranotas = PranotaOb::whereIn('id', $pranotaIds)->get();
            Log::info('Found pranotas', ['count' => $pranotas->count(), 'ids' => $pranotaIds]);

            foreach ($pranotas as $pranota) {
                if ($pranota->status !== 'unpaid') {
                    throw new \Exception("Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
                }
            }

            // Calculate total biaya pranota (total sebelum dikurangi DP)
            $totalBiayaPranota = 0;
            foreach ($pranotas as $pranota) {
                $totalBiayaPranota += $pranota->calculateTotalAmount();
            }
            Log::info('Calculated total biaya pranota', ['total' => $totalBiayaPranota]);

            // Get DP data
            $dpId = $request->input('dp_id');
            $dpAmount = 0;
            $selectedDp = null;
            
            if ($dpId) {
                $selectedDp = \App\Models\PembayaranOb::find($dpId);
                if ($selectedDp) {
                    $dpAmount = $selectedDp->dp_amount ?? 0;
                    Log::info('DP found', ['dp_id' => $dpId, 'dp_amount' => $dpAmount]);
                }
            }

            // Total pembayaran = Total Biaya - DP (SISA yang harus dibayar)
            $totalPembayaran = $totalBiayaPranota - $dpAmount;
            Log::info('Calculated total pembayaran (after DP)', [
                'total_biaya' => $totalBiayaPranota,
                'dp_amount' => $dpAmount,
                'total_pembayaran' => $totalPembayaran
            ]);

            // Check for duplicate nomor_pembayaran
            $existingPayment = PembayaranPranotaOb::where('nomor_pembayaran', $request->nomor_pembayaran)->first();
            if ($existingPayment) {
                // If duplicate found, generate a new number
                $request->merge(['nomor_pembayaran' => PembayaranPranotaOb::generateNomorPembayaran()]);
            }

            // Decode breakdown supir from JSON string
            $breakdownSupir = null;
            if ($request->filled('breakdown_supir')) {
                $breakdownSupir = json_decode($request->input('breakdown_supir'), true);
            }

            // Get account names for journaling
            $akunBiaya = \App\Models\Coa::findOrFail($request->akun_coa_id);
            $akunBank = \App\Models\Coa::findOrFail($request->akun_bank_id);

            // Create pembayaran record
            $pembayaran = PembayaranPranotaOb::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'nomor_cetakan' => 1,
                'bank' => $akunBank->nama_akun,
                'jenis_transaksi' => $request->debit_kredit,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved',
                'pranota_ob_ids' => $pranotaIds,
                'pembayaran_ob_id' => $dpId,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'dp_amount' => $dpAmount,
                'total_biaya_pranota' => $totalBiayaPranota,
                'breakdown_supir' => $breakdownSupir,
                'akun_coa_id' => $request->akun_coa_id,
                'akun_bank_id' => $request->akun_bank_id
            ]);
            Log::info('Pembayaran record created', ['id' => $pembayaran->id]);

            // Update pranota status to paid
            foreach ($pranotas as $pranota) {
                $pranota->update(['status' => 'paid']);
                Log::info('Pranota status updated', ['pranota_id' => $pranota->id]);
            }

            // Catat transaksi menggunakan double-entry COA
            $totalSetelahPenyesuaian = $totalPembayaran + $penyesuaian;
            $tanggalTransaksi = $request->tanggal_kas;

            $keterangan = "Pembayaran Pranota OB - " . $request->nomor_pembayaran;
            if ($request->keterangan) {
                $keterangan .= " | " . $request->keterangan;
            }
            if ($request->alasan_penyesuaian) {
                $keterangan .= " | Penyesuaian: " . $request->alasan_penyesuaian;
            }

            // Catat transaksi double-entry berdasarkan pilihan Debit/Credit
            if ($request->debit_kredit === 'credit') {
                // KREDIT: Biaya OB (Debit) dan Bank (Kredit)
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $akunBiaya->nama_akun, 'jumlah' => $totalSetelahPenyesuaian],
                    ['nama_akun' => $akunBank->nama_akun, 'jumlah' => $totalSetelahPenyesuaian],
                    $tanggalTransaksi,
                    $request->nomor_pembayaran,
                    'Pembayaran Pranota OB',
                    $keterangan
                );
            } else {
                // DEBIT: Bank (Debit) dan Biaya OB (Kredit)
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $akunBank->nama_akun, 'jumlah' => $totalSetelahPenyesuaian],
                    ['nama_akun' => $akunBiaya->nama_akun, 'jumlah' => $totalSetelahPenyesuaian],
                    $tanggalTransaksi,
                    $request->nomor_pembayaran,
                    'Pembayaran Pranota OB',
                    $keterangan
                );
            }

            DB::commit();
            Log::info('Transaction committed successfully');

            $message = "Pembayaran pranota OB berhasil dibuat dengan nomor: {$request->nomor_pembayaran}. ";
            $message .= "Total pranota: " . count($pranotaIds) . ". ";
            $message .= "Status: Sudah dibayar.";

            return redirect()->route('pembayaran-pranota-ob.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in pembayaran pranota OB store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $pembayaran = PembayaranPranotaOb::with(['pranotaObs'])->findOrFail($id);

        return view('pembayaran-pranota-ob.print', compact('pembayaran'));
    }

    public function edit($id)
    {
        $pembayaran = PembayaranPranotaOb::with('pembayaranOb')->findOrFail($id);

        // Get all COA accounts for Akun Biaya
        $akunBiaya = Coa::orderBy('kode_nomor')->get();

        // Get Bank/Kas accounts only, sorted by account number
        $akunBank = Coa::where(function($query) {
                $query->where('tipe_akun', 'Kas/Bank')
                      ->orWhere('tipe_akun', 'Bank/Kas')
                      ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                      ->orWhere('tipe_akun', 'LIKE', '%Bank%');
            })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        return view('pembayaran-pranota-ob.edit', compact('pembayaran', 'akunBank', 'akunBiaya'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_kas' => 'required|date',
            'bank' => 'nullable|string|max:255',
            'jenis_transaksi' => 'nullable|string',
            'debit_kredit' => 'required|in:debit,credit',
            'akun_coa_id' => 'required|exists:akun_coa,id',
            'akun_bank_id' => 'required|exists:akun_coa,id',
            'penyesuaian' => 'nullable|string',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'breakdown_supir' => 'nullable|json',
        ]);

        try {
            DB::beginTransaction();

            $pembayaran = PembayaranPranotaOb::findOrFail($id);

            // Parse penyesuaian from formatted string to number
            $penyesuaian = 0;
            if (!empty($validated['penyesuaian'])) {
                $penyesuaian = floatval(str_replace(['.', ','], ['', '.'], $validated['penyesuaian']));
            }

            // Parse breakdown_supir to calculate total
            $breakdownSupir = [];
            if (!empty($validated['breakdown_supir'])) {
                $breakdownSupir = json_decode($validated['breakdown_supir'], true) ?? [];
            }

            // Calculate total from breakdown_supir
            $totalPembayaran = 0;
            if (!empty($breakdownSupir)) {
                foreach ($breakdownSupir as $breakdown) {
                    $totalPembayaran += (float)($breakdown['grand_total'] ?? $breakdown['sisa'] ?? 0);
                }
            } else {
                // If no breakdown, keep original total
                $totalPembayaran = $pembayaran->total_pembayaran;
            }

            // Update pembayaran record
            $updateData = [
                'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                'tanggal_kas' => $validated['tanggal_kas'],
                'bank' => \App\Models\Coa::find($request->akun_bank_id)?->nama_akun ?? ($validated['bank'] ?? null),
                'jenis_transaksi' => $request->input('debit_kredit', $validated['jenis_transaksi'] ?? null),
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
                'alasan_penyesuaian' => $validated['alasan_penyesuaian'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'breakdown_supir' => !empty($breakdownSupir) ? $breakdownSupir : null,
                'akun_coa_id' => $request->akun_coa_id,
                'akun_bank_id' => $request->akun_bank_id,
                'updated_by' => Auth::id(),
            ];

            $pembayaran->update($updateData);

            // Note: COA transaction update tidak dilakukan di sini
            // karena perubahan hanya pada data pembayaran, bukan transaksi baru
            // Jika diperlukan reversal dan re-record, implementasi terpisah

            DB::commit();

            return redirect()->route('pembayaran-pranota-ob.index')
                ->with('success', 'Pembayaran pranota OB berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pembayaran pranota OB: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate pembayaran: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Check permission
        if (!Gate::allows('pembayaran-pranota-ob-delete')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus pembayaran pranota OB.');
        }

        try {
            DB::beginTransaction();

            $pembayaran = PembayaranPranotaOb::findOrFail($id);

            // Get associated pranota IDs (model has 'pranota_ob_ids' => 'array' cast)
            $pranotaIds = $pembayaran->pranota_ob_ids;
            
            // Handle double encoded JSON if necessary (workaround for existing data)
            if (is_string($pranotaIds)) {
                $pranotaIds = json_decode($pranotaIds, true) ?? [];
            }
            
            $pranotaIds = is_array($pranotaIds) ? $pranotaIds : [];

            // Restore pranota status to unpaid
            if (!empty($pranotaIds)) {
                PranotaOb::whereIn('id', $pranotaIds)->update(['status' => 'unpaid']);
                Log::info('Restored pranota statuses to unpaid', ['pranota_ids' => $pranotaIds]);
            }

            // Delete associated COA transactions
            $success = $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran);
            if (!$success) {
                Log::warning('Failed to delete some COA transactions or transactions not found', ['nomor_pembayaran' => $pembayaran->nomor_pembayaran]);
            }

            // Delete the payment record
            $nomorPembayaran = $pembayaran->nomor_pembayaran;
            $pembayaran->delete();

            DB::commit();
            Log::info('Pembayaran pranota OB deleted successfully', ['id' => $id, 'nomor_pembayaran' => $nomorPembayaran]);

            return redirect()->route('pembayaran-pranota-ob.index')
                ->with('success', "Pembayaran nomor {$nomorPembayaran} berhasil dihapus dan status pranota dikembalikan.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembayaran pranota OB: ' . $e->getMessage());
            return redirect()->route('pembayaran-pranota-ob.index')
                ->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Sync payment data with COA transactions.
     */
    public function syncCoa(PembayaranPranotaOb $pembayaranPranotaOb)
    {
        $user = Auth::user();
        if (!$user || !$user->can('pembayaran-pranota-ob-edit')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan ini.');
        }

        DB::beginTransaction();
        try {
            // 1. Delete existing COA transactions for this payment reference
            $this->coaTransactionService->deleteTransactionByReference($pembayaranPranotaOb->nomor_pembayaran);

            // 2. Prepare data for re-sync
            $totalPembayaran = $pembayaranPranotaOb->total_setelah_penyesuaian ?? $pembayaranPranotaOb->total_pembayaran;
            $akunBankId = $pembayaranPranotaOb->akun_bank_id;
            $akunCoaId = $pembayaranPranotaOb->akun_coa_id;
            $debitKredit = strtolower($pembayaranPranotaOb->jenis_transaksi);
            $keterangan = "Pembayaran Pranota OB - " . $pembayaranPranotaOb->nomor_pembayaran . " (Synced)";

            // 3. Record Double Entry
            if ($debitKredit == 'debit') {
                // Jenis Debit (Bank Bertambah): DEBIT Bank, KREDIT Biaya OB
                $this->coaTransactionService->recordDoubleEntry(
                    ['id' => $akunBankId, 'jumlah' => $totalPembayaran],
                    ['id' => $akunCoaId, 'jumlah' => $totalPembayaran],
                    $pembayaranPranotaOb->tanggal_kas,
                    $pembayaranPranotaOb->nomor_pembayaran,
                    'Pembayaran Pranota OB',
                    $keterangan
                );
            } else {
                // Jenis Kredit (Bank Berkurang): DEBIT Biaya OB, KREDIT Bank
                $this->coaTransactionService->recordDoubleEntry(
                    ['id' => $akunCoaId, 'jumlah' => $totalPembayaran],
                    ['id' => $akunBankId, 'jumlah' => $totalPembayaran],
                    $pembayaranPranotaOb->tanggal_kas,
                    $pembayaranPranotaOb->nomor_pembayaran,
                    'Pembayaran Pranota OB',
                    $keterangan
                );
            }

            Log::info('Double Entry Accounting RE-SYNCED for Pembayaran Pranota OB', [
                'nomor_pembayaran' => $pembayaranPranotaOb->nomor_pembayaran,
                'total_pembayaran' => $totalPembayaran,
                'synced_by' => $user->name
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Transaksi COA untuk ' . $pembayaranPranotaOb->nomor_pembayaran . ' berhasil disinkronisasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncing COA for payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal sinkronisasi COA: ' . $e->getMessage());
        }
    }

    /**
     * Update payment total based on current pranota totals.
     */
    public function updateTotal(PembayaranPranotaOb $pembayaranPranotaOb)
    {
        $user = Auth::user();
        if (!$user || !$user->can('pembayaran-pranota-ob-edit')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan ini.');
        }

        DB::beginTransaction();
        try {
            $pranotaObs = $pembayaranPranotaOb->pranota_obs; // Use accessor
            
            $newTotal = 0;
            $newBreakdownMap = [];
            
            foreach ($pranotaObs as $pranota) {
                $newTotal += $pranota->calculateTotalAmount();
                
                // Recalculate breakdown from items
                $items = $pranota->getEnrichedItems();
                foreach ($items as $item) {
                    $supir = strtoupper(trim($item['supir'] ?? ''));
                    if (empty($supir) || $supir === '-') $supir = 'BELUM DITENTUKAN';
                    
                    if (!isset($newBreakdownMap[$supir])) {
                        $newBreakdownMap[$supir] = [
                            'nama_supir' => $supir,
                            'jumlah_item' => 0,
                            'total_biaya' => 0,
                            'dp' => 0, // DPs are tricky to re-calculate without original context
                            'sisa' => 0,
                            'potongan_utang' => 0,
                            'potongan_tabungan' => 0,
                            'potongan_bpjs' => 0,
                            'grand_total' => 0
                        ];
                    }
                    
                    $newBreakdownMap[$supir]['jumlah_item'] += 1;
                    $newBreakdownMap[$supir]['total_biaya'] += (float)($item['biaya'] ?? 0);
                }
            }

            // Sync with existing breakdown (preserved DP and deductions)
            $oldBreakdown = $pembayaranPranotaOb->breakdown_supir ?? [];
            $finalBreakdown = [];
            
            foreach ($newBreakdownMap as $supir => $newData) {
                // Find matching supir in old breakdown to preserve DP, etc.
                $oldData = collect($oldBreakdown)->first(fn($v) => strtoupper(trim($v['nama_supir'] ?? '')) === $supir);
                
                if ($oldData) {
                    $newData['dp'] = $oldData['dp'] ?? 0;
                    $newData['potongan_utang'] = $oldData['potongan_utang'] ?? 0;
                    $newData['potongan_tabungan'] = $oldData['potongan_tabungan'] ?? 0;
                    $newData['potongan_bpjs'] = $oldData['potongan_bpjs'] ?? 0;
                }
                
                $newData['sisa'] = $newData['total_biaya'] - $newData['dp'];
                $newData['grand_total'] = $newData['sisa'] - $newData['potongan_utang'] - $newData['potongan_tabungan'] - $newData['potongan_bpjs'];
                
                $finalBreakdown[] = $newData;
            }

            // Update main payment record
            $penyesuaian = $pembayaranPranotaOb->penyesuaian ?? 0;
            $pembayaranPranotaOb->update([
                'total_pembayaran' => $newTotal,
                'total_biaya_pranota' => $newTotal,
                'total_setelah_penyesuaian' => $newTotal + $penyesuaian,
                'breakdown_supir' => $finalBreakdown,
                'updated_by' => Auth::id()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Total pembayaran dan breakdown supir berhasil diperbarui sesuai data pranota terbaru.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment total: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui total: ' . $e->getMessage());
        }
    }
}
