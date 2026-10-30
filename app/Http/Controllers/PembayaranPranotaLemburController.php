<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\NomorTerakhir;
use App\Models\PembayaranPranotaLembur;
use App\Models\PranotaLembur;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'updater',
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
            'cancelled' => 'Dibatalkan',
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
                $request->end_date,
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
        $akunCoa = Coa::where(function ($q) {
            $q->where('tipe_akun', 'LIKE', '%bank%')
                ->orWhere('nama_akun', 'LIKE', '%bank%')
                ->orWhere('nama_akun', 'LIKE', '%kas%');
        })
            ->orderBy('nama_akun')
            ->get();
        $akunBiaya = Coa::orderBy('nomor_akun')->get(['id', 'nomor_akun', 'nama_akun']);
        $defaultAkunCoaId = Coa::where('nama_akun', 'Biaya Lembur Jakarta')->value('id')
            ?? Coa::where('nama_akun', 'LIKE', 'Biaya Lembur%')->value('id');

        // Generate nomor pembayaran using SIS modul
        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-lembur.create', compact('pranotaLemburs', 'nomorPembayaran', 'akunCoa', 'akunBiaya', 'defaultAkunCoaId'));
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
            'akun_coa_id' => ['required', 'integer', Rule::exists('akun_coa', 'id')],
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'nomor_cetakan' => 'nullable|integer|min:1|max:9',
        ]);
        $biayaAccount = Coa::findOrFail($validated['akun_coa_id']);
        if ($biayaAccount->nama_akun === $validated['bank']) {
            throw ValidationException::withMessages(['akun_coa_id' => 'Akun biaya harus berbeda dari akun bank.']);
        }

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
                    'updated_by' => Auth::id(),
                ]);
            }

            // Double-entry accounting
            $totalPembayaran = $validated['total_tagihan_setelah_penyesuaian'] ?? $validated['total_pembayaran'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $keterangan = 'Pembayaran Pranota Lembur - '.$nomorPembayaran;

            if ($jenisTransaksi == 'Debit') {
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    ['nama_akun' => $biayaAccount->nama_akun, 'coa_id' => $biayaAccount->id, 'jumlah' => $totalPembayaran],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Lembur',
                    $keterangan
                );
            } else {
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $biayaAccount->nama_akun, 'coa_id' => $biayaAccount->id, 'jumlah' => $totalPembayaran],
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Lembur',
                    $keterangan
                );
            }
            if (! $doubleEntryResult) {
                throw new \RuntimeException('Jurnal pembayaran gagal dicatat.');
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('success', 'Pembayaran '.$nomorPembayaran.' berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Lembur: '.$e->getMessage());

            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranPranotaLembur $pembayaranPranotaLembur)
    {
        $pembayaranPranotaLembur->load(['pranotaLemburs', 'akunCoa', 'createdBy', 'updatedBy']);
        $akunBiayaTampil = $pembayaranPranotaLembur->akunCoa
            ?? Coa::find($this->legacyExpenseAccountId($pembayaranPranotaLembur));

        return view('pembayaran-pranota-lembur.show', compact('pembayaranPranotaLembur', 'akunBiayaTampil'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, PembayaranPranotaLembur $pembayaranPranotaLembur)
    {
        if ($pembayaranPranotaLembur->isCancelled()) {
            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('error', 'Pembayaran yang dibatalkan tidak dapat diubah.');
        }

        $pembayaranPranotaLembur->load(['pranotaLemburs.suratJalans', 'pranotaLemburs.suratJalanBongkarans']);

        $currentIds = $pembayaranPranotaLembur->pranotaLemburs->pluck('id')->all();
        $pranotaLemburQuery = PranotaLembur::where(function ($query) use ($currentIds) {
            $query->whereIn('id', $currentIds)
                ->orWhere(function ($available) {
                    $available->where('status', '!=', PranotaLembur::STATUS_CANCELLED)
                        ->whereDoesntHave('pembayaranPranotaLemburs');
                });
        });
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaLemburQuery->where(function ($query) use ($request, $currentIds) {
                $query->whereIn('id', $currentIds)
                    ->orWhereBetween('tanggal_pranota', [$request->start_date, $request->end_date]);
            });
        }
        $pranotaLemburs = $pranotaLemburQuery->orderBy('tanggal_pranota', 'desc')->get();

        $akunCoa = Coa::where(function ($q) {
            $q->where('tipe_akun', 'LIKE', '%bank%')
                ->orWhere('nama_akun', 'LIKE', '%bank%')
                ->orWhere('nama_akun', 'LIKE', '%kas%');
        })
            ->orderBy('nama_akun')
            ->get();
        $akunBiaya = Coa::orderBy('nomor_akun')->get(['id', 'nomor_akun', 'nama_akun']);
        $selectedAkunCoaId = $pembayaranPranotaLembur->akun_coa_id
            ?? $this->legacyExpenseAccountId($pembayaranPranotaLembur)
            ?? Coa::where('nama_akun', 'Biaya Lembur Jakarta')->value('id');

        return view('pembayaran-pranota-lembur.edit', compact('pembayaranPranotaLembur', 'pranotaLemburs', 'akunCoa', 'akunBiaya', 'selectedAkunCoaId'));
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

        $validated = $request->validate([
            'pranota_lembur_ids' => ['required', 'array', 'min:1'],
            'pranota_lembur_ids.*' => ['required', 'integer', 'distinct', 'exists:pranota_lemburs,id'],
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'bank' => ['required', 'string', 'max:255', Rule::exists('akun_coa', 'nama_akun')],
            'akun_coa_id' => ['required', 'integer', Rule::exists('akun_coa', 'id')],
            'jenis_transaksi' => ['required', Rule::in(['Debit', 'Kredit'])],
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ]);
        $biayaAccount = Coa::findOrFail($validated['akun_coa_id']);
        if ($biayaAccount->nama_akun === $validated['bank']) {
            throw ValidationException::withMessages(['akun_coa_id' => 'Akun biaya harus berbeda dari akun bank.']);
        }

        try {
            DB::transaction(function () use ($validated, $pembayaranPranotaLembur, $biayaAccount) {
                $oldPranotas = $pembayaranPranotaLembur->pranotaLemburs()->get();
                $oldIds = $oldPranotas->pluck('id')->all();
                $newIds = array_map('intval', $validated['pranota_lembur_ids']);
                $selectedPranotas = PranotaLembur::with('pembayaranPranotaLemburs')
                    ->whereIn('id', $newIds)->lockForUpdate()->get();
                if ($selectedPranotas->count() !== count($newIds)) {
                    throw ValidationException::withMessages([
                        'pranota_lembur_ids' => 'Salah satu pranota tidak ditemukan.',
                    ]);
                }

                foreach ($selectedPranotas as $pranota) {
                    if (! in_array($pranota->id, $oldIds)
                        && ($pranota->status === PranotaLembur::STATUS_CANCELLED
                            || $pranota->pembayaranPranotaLemburs->isNotEmpty())) {
                        throw ValidationException::withMessages([
                            'pranota_lembur_ids' => 'Salah satu pranota sudah dibayar atau dibatalkan.',
                        ]);
                    }
                }

                $total = round((float) $selectedPranotas->sum('total_setelah_adjustment'), 2);
                $adjustment = (float) ($validated['total_tagihan_penyesuaian'] ?? 0);
                $finalTotal = round($total + $adjustment, 2);
                if ($finalTotal < 0) {
                    throw ValidationException::withMessages([
                        'total_tagihan_penyesuaian' => 'Total akhir pembayaran tidak boleh negatif.',
                    ]);
                }

                if (! $this->coaTransactionService->deleteTransactionByReference($pembayaranPranotaLembur->nomor_pembayaran)) {
                    throw new \RuntimeException('Jurnal lama gagal dibatalkan.');
                }

                $pembayaranPranotaLembur->update([
                    'nomor_accurate' => $validated['nomor_accurate'] ?? null,
                    'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                    'bank' => $validated['bank'],
                    'akun_coa_id' => $biayaAccount->id,
                    'jenis_transaksi' => $validated['jenis_transaksi'],
                    'total_pembayaran' => $total,
                    'total_tagihan_penyesuaian' => $adjustment,
                    'total_tagihan_setelah_penyesuaian' => $finalTotal,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'updated_by' => Auth::id(),
                ]);

                $pivotData = $selectedPranotas->mapWithKeys(fn ($pranota) => [
                    $pranota->id => ['subtotal' => $pranota->total_setelah_adjustment],
                ])->all();
                $pembayaranPranotaLembur->pranotaLemburs()->sync($pivotData);
                foreach ($oldPranotas as $pranota) {
                    if (! in_array($pranota->id, $newIds)) {
                        $pranota->update(['status' => PranotaLembur::STATUS_APPROVED, 'updated_by' => Auth::id()]);
                    }
                }
                foreach ($selectedPranotas as $pranota) {
                    $pranota->update(['status' => PranotaLembur::STATUS_PAID, 'updated_by' => Auth::id()]);
                }

                $bankAccount = ['nama_akun' => $validated['bank'], 'jumlah' => $finalTotal];
                $expenseAccount = ['nama_akun' => $biayaAccount->nama_akun, 'coa_id' => $biayaAccount->id, 'jumlah' => $finalTotal];
                $debit = $validated['jenis_transaksi'] === 'Debit' ? $bankAccount : $expenseAccount;
                $credit = $validated['jenis_transaksi'] === 'Debit' ? $expenseAccount : $bankAccount;
                if (! $this->coaTransactionService->recordDoubleEntry(
                    $debit,
                    $credit,
                    $validated['tanggal_pembayaran'],
                    $pembayaranPranotaLembur->nomor_pembayaran,
                    'Pembayaran Pranota Lembur',
                    'Pembayaran Pranota Lembur - '.$pembayaranPranotaLembur->nomor_pembayaran
                )) {
                    throw new \RuntimeException('Jurnal baru gagal dicatat.');
                }
            });

            return redirect()->route('pembayaran-pranota-lembur.index')
                ->with('success', 'Pembayaran berhasil diperbarui.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating Pembayaran Pranota Lembur: '.$e->getMessage());

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
            Log::error('Error deleting Pembayaran Pranota Lembur: '.$e->getMessage());

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
                'keterangan' => 'Nomor Pembayaran',
            ]
        );

        $now = now();
        $bulan = $now->format('m');
        $tahun = $now->format('y');
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);

        return "SIS-{$bulan}-{$tahun}-{$runningNumber}";
    }

    private function legacyExpenseAccountId(PembayaranPranotaLembur $payment): ?int
    {
        return CoaTransaction::where('nomor_referensi', $payment->nomor_pembayaran)
            ->whereHas('coa', fn ($query) => $query->where('nama_akun', 'LIKE', 'Biaya Lembur%'))
            ->value('coa_id');
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
                'nomor_pembayaran' => $nomorPembayaran,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
