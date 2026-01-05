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
        if (!$request->has('kapal') || !$request->has('voyage') || !$request->has('dp')) {
            return redirect()->route('pembayaran-pranota-ob.select-criteria')
                ->with('error', 'Silakan pilih kriteria terlebih dahulu.');
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
                            $dpSupirData[$supir->nama_lengkap] = floatval($jumlah);
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

        // Get Bank/Kas accounts only, sorted by account number
        $akunCoa = Coa::where(function($query) {
                $query->where('tipe_akun', 'Kas/Bank')
                      ->orWhere('tipe_akun', 'Bank/Kas')
                      ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                      ->orWhere('tipe_akun', 'LIKE', '%Bank%');
            })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        return view('pembayaran-pranota-ob.create', compact('pranotaList', 'akunCoa', 'selectedDp', 'dpSupirData'));
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
                'bank' => 'required|string|max:255',
                'jenis_transaksi' => 'required|in:debit,credit',
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
            $totalBiayaPranota = $pranotas->sum('total_amount');
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

            // Create pembayaran record
            $pembayaran = PembayaranPranotaOb::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'nomor_cetakan' => 1,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved',
                'pranota_ob_ids' => json_encode($pranotaIds),
                'pembayaran_ob_id' => $dpId,
                'kapal' => $request->kapal,
                'voyage' => $request->voyage,
                'dp_amount' => $dpAmount,
                'total_biaya_pranota' => $totalBiayaPranota,
                'breakdown_supir' => $breakdownSupir
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

            // Catat transaksi double-entry: Biaya OB (Debit) dan Bank (Kredit)
            $this->coaTransactionService->recordDoubleEntry(
                ['nama_akun' => 'Biaya OB', 'jumlah' => $totalSetelahPenyesuaian],
                ['nama_akun' => $request->bank, 'jumlah' => $totalSetelahPenyesuaian],
                $tanggalTransaksi,
                $request->nomor_pembayaran,
                'Pembayaran Pranota OB',
                $keterangan
            );

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
        
        return view('pembayaran-pranota-ob.edit', compact('pembayaran'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_kas' => 'required|date',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:debit,credit',
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
                'nomor_accurate' => $validated['nomor_accurate'],
                'tanggal_kas' => $validated['tanggal_kas'],
                'bank' => $validated['bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
                'alasan_penyesuaian' => $validated['alasan_penyesuaian'],
                'keterangan' => $validated['keterangan'],
                'breakdown_supir' => !empty($breakdownSupir) ? json_encode($breakdownSupir) : null,
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
        // Placeholder for future implementation
        return redirect()->route('pembayaran-pranota-ob.index')
            ->with('info', 'Fitur delete sedang dalam pengembangan');
    }
}
