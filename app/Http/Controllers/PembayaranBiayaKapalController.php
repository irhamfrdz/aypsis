<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Models\PembayaranBiayaKapal;
use App\Services\CoaTransactionService;
use App\Services\TemasPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PembayaranBiayaKapalController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-biaya-kapal-view')->only(['index', 'show', 'temasSummary']);
        $this->middleware('can:pembayaran-biaya-kapal-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-biaya-kapal-edit')->only(['edit', 'update', 'syncCoa']);
        $this->middleware('can:pembayaran-biaya-kapal-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BiayaKapal::with(['klasifikasiBiaya', 'pembayarans']);

        // Filter by status pembayaran
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        // Search by invoice number, vessel name or vendor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_invoice', 'like', "%{$search}%")
                    ->orWhere('nama_kapal', 'like', "%{$search}%")
                    ->orWhere('nama_vendor', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        $biayaKapalList = $query->orderBy('tanggal', 'desc')->paginate(15);

        $statuses = [
            'pending' => 'Belum Lunas',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan',
        ];

        return view('pembayaran-biaya-kapal.index', compact('biayaKapalList', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $biayaKapalQuery = BiayaKapal::query()->pending();

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $biayaKapalQuery->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        // If biaya_kapal_id is provided, filter for specific invoice
        if ($request->filled('biaya_kapal_id')) {
            $biayaKapalQuery->where('id', $request->biaya_kapal_id);
        }

        $biayaKapals = $biayaKapalQuery
            ->with(['klasifikasiBiaya'])
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        // Get akun COA for bank selection
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();

        $nomorPembayaran = $this->generateNomorPembayaran();

        return view('pembayaran-biaya-kapal.create', compact('biayaKapals', 'nomorPembayaran', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'biaya_kapal_ids' => ['required', 'array', 'min:1'],
            'biaya_kapal_ids.*' => ['integer', 'distinct', 'exists:biaya_kapals,id'],
            'tanggal_pembayaran' => 'required|date_format:Y-m-d',
            'payment_mode' => 'nullable|in:lunas,dp,pelunasan_dp',
            'nominal_dp' => 'required_if:payment_mode,dp|nullable|numeric|decimal:0,2|gt:0',
            'dp_item_id' => 'required_if:payment_mode,pelunasan_dp|nullable|integer|exists:pembayaran_biaya_kapal_items,id',
            'jenis_transaksi' => ['required', Rule::in(['debit', 'kredit'])],
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'nomor_accurate' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $temasPayments = app(TemasPaymentService::class);
            $mode = $validated['payment_mode'] ?? 'lunas';
            $invoices = BiayaKapal::whereIn('id', $validated['biaya_kapal_ids'])
                ->orderBy('id')->lockForUpdate()->get();
            if ($invoices->count() !== count($validated['biaya_kapal_ids'])) {
                throw ValidationException::withMessages(['biaya_kapal_ids' => 'Invoice tidak tersedia atau sudah dihapus.']);
            }
            if ($mode !== 'lunas' && ($invoices->count() !== 1 || ! $temasPayments->isTemas($invoices->first()))) {
                throw ValidationException::withMessages(['payment_mode' => 'Pilih tepat satu invoice TEMAS untuk DP atau pelunasan DP.']);
            }
            $allocations = [];
            $hasTemas = false;
            foreach ($invoices as $invoice) {
                if ($temasPayments->isTemas($invoice)) {
                    $hasTemas = true;
                    if ($request->jenis_transaksi !== 'kredit' || (float) $request->total_tagihan_penyesuaian != 0) {
                        throw ValidationException::withMessages(['jenis_transaksi' => 'Pembayaran TEMAS menggunakan kredit (uang keluar). Penyesuaian biaya harus dicatat pada invoice sebelum pembayaran.']);
                    }
                    $allocations[$invoice->id] = $temasPayments->prepare(
                        $invoice, $mode, $validated['nominal_dp'] ?? null,
                        isset($validated['dp_item_id']) ? (int) $validated['dp_item_id'] : null,
                        $validated['tanggal_pembayaran']
                    );
                } else {
                    $allocations[$invoice->id] = ['nominal' => $invoice->total_biaya ?? $invoice->nominal];
                }
            }
            $paymentTotal = array_sum(array_map(fn ($item) => $temasPayments->cents($item['nominal']), $allocations));
            if ($hasTemas && $temasPayments->cents($validated['total_pembayaran']) !== $paymentTotal) {
                throw ValidationException::withMessages(['total_pembayaran' => 'Total pembayaran tidak sesuai DP atau sisa tagihan terbaru. Muat ulang ringkasan pembayaran.']);
            }
            if ($hasTemas) {
                if ($invoices->contains(fn ($invoice) => ! $temasPayments->isTemas($invoice))) {
                    throw ValidationException::withMessages(['biaya_kapal_ids' => 'Pisahkan pembayaran invoice TEMAS dari jenis biaya lainnya.']);
                }
                $request->validate(['bank' => 'required|string|max:255']);
            }
            // Get or create PBK modul
            $modulNomor = NomorTerakhir::firstOrCreate(
                ['modul' => 'PBK'],
                ['nomor_terakhir' => 0, 'keterangan' => 'pembayaran biaya kapal']
            );
            $modulNomor = NomorTerakhir::whereKey($modulNomor->id)->lockForUpdate()->firstOrFail();

            // Generate number
            $nomorPembayaran = $this->generateNomorPembayaran();
            $modulNomor->increment('nomor_terakhir');

            $pembayaran = PembayaranBiayaKapal::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'total_pembayaran' => $hasTemas ? $paymentTotal / 100 : $request->total_pembayaran,
                'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian ?? 0,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status_pembayaran' => 'paid',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($invoices as $biayaKapal) {
                $pembayaran->items()->create(array_merge($allocations[$biayaKapal->id], ['biaya_kapal_id' => $biayaKapal->id]));
                if ($temasPayments->isTemas($biayaKapal)) {
                    $temasPayments->syncInvoice($biayaKapal);
                } else {
                    $biayaKapal->update(['status_pembayaran' => 'paid']);
                }
            }

            // TEMAS payments track invoice balances without posting COA transactions.
            if (! $hasTemas && method_exists($this->coaTransactionService, 'pembayaranBiayaKapal')) {
                $this->coaTransactionService->pembayaranBiayaKapal($pembayaran);
            }

            DB::commit();

            return redirect()->route('pembayaran-biaya-kapal.index')
                ->with('success', 'Pembayaran biaya kapal berhasil disimpan.');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing pembayaran biaya kapal: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pembayaran = PembayaranBiayaKapal::with(['biayaKapals.klasifikasiBiaya', 'creator'])->findOrFail($id);

        return view('pembayaran-biaya-kapal.show', compact('pembayaran'));
    }

    public function temasSummary(BiayaKapal $biayaKapal, TemasPaymentService $payments)
    {
        abort_unless($payments->isTemas($biayaKapal), 404);

        return response()->json($payments->summary($biayaKapal));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pembayaran = PembayaranBiayaKapal::with(['biayaKapals.klasifikasiBiaya'])->findOrFail($id);

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();

        return view('pembayaran-biaya-kapal.edit', compact('pembayaran', 'akunCoa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranBiayaKapal::findOrFail($id);

        $validated = $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'bank' => 'required|string',
            'jenis_transaksi' => ['required', Rule::in(['debit', 'kredit'])],
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'nomor_accurate' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $invoices = BiayaKapal::whereIn('id', $pembayaran->items()->pluck('biaya_kapal_id'))
                ->orderBy('id')->lockForUpdate()->get();
            $pembayaran = PembayaranBiayaKapal::whereKey($id)->lockForUpdate()->firstOrFail();
            $hasTemas = $invoices->contains(fn ($invoice) => app(TemasPaymentService::class)->isTemas($invoice));
            if ($hasTemas && (
                $request->date('tanggal_pembayaran')->format('Y-m-d') !== $pembayaran->tanggal_pembayaran->format('Y-m-d')
                || $request->bank !== $pembayaran->bank
                || $request->jenis_transaksi !== $pembayaran->jenis_transaksi
                || (float) $request->total_tagihan_penyesuaian !== (float) $pembayaran->total_tagihan_penyesuaian
            )) {
                throw ValidationException::withMessages(['payment_mode' => 'Tanggal, bank, dan nilai pembayaran TEMAS tidak dapat diubah. Batalkan pembayaran lalu buat kembali untuk koreksi keuangan.']);
            }
            $pembayaran->update([
                'nomor_accurate' => $request->nomor_accurate,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'total_pembayaran' => $hasTemas ? $pembayaran->total_pembayaran : $pembayaran->items()->sum('nominal'),
                'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian ?? 0,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            // Re-run accounting if exists
            if (! $hasTemas && method_exists($this->coaTransactionService, 'pembayaranBiayaKapal')) {
                $this->coaTransactionService->pembayaranBiayaKapal($pembayaran);
            }

            DB::commit();

            return redirect()->route('pembayaran-biaya-kapal.index')
                ->with('success', 'Pembayaran biaya kapal berhasil diperbarui.');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pembayaran biaya kapal: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pembayaran = PembayaranBiayaKapal::findOrFail($id);

        DB::beginTransaction();
        try {
            $invoices = BiayaKapal::whereIn('id', $pembayaran->items()->pluck('biaya_kapal_id'))
                ->orderBy('id')->lockForUpdate()->get();
            $pembayaran = PembayaranBiayaKapal::whereKey($id)->lockForUpdate()->firstOrFail();
            $temasPayments = app(TemasPaymentService::class);
            $temasPayments->assertPaymentCanBeCancelled($pembayaran);
            // Restore status of associated biaya kapals
            foreach ($invoices as $biayaKapal) {
                if ($temasPayments->isTemas($biayaKapal)) {
                    continue;
                }
                $biayaKapal->update([
                    'status_pembayaran' => 'pending',
                ]);
            }

            // Preserve TEMAS items and their DP references for cancelled-payment history.
            $otherIds = $invoices->reject(fn ($invoice) => $temasPayments->isTemas($invoice))->pluck('id');
            $pembayaran->biayaKapals()->detach($otherIds);

            // Delete associated COA transactions
            if (! $invoices->contains(fn ($invoice) => $temasPayments->isTemas($invoice))) {
                $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran);
            }

            // Delete payment record
            $pembayaran->delete();
            foreach ($invoices as $biayaKapal) {
                if ($temasPayments->isTemas($biayaKapal)) {
                    $temasPayments->syncInvoice($biayaKapal);
                }
            }

            DB::commit();

            return redirect()->route('pembayaran-biaya-kapal.index')
                ->with('success', 'Pembayaran berhasil dibatalkan dan dihapus.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membatalkan pembayaran: '.$e->getMessage());
        }
    }

    /**
     * Synchronize the payment to COA.
     */
    public function syncCoa($id)
    {
        $pembayaran = PembayaranBiayaKapal::findOrFail($id);

        DB::beginTransaction();

        try {
            // 1. Delete existing COA transactions for this reference number
            $invoices = BiayaKapal::whereIn('id', $pembayaran->items()->pluck('biaya_kapal_id'))
                ->orderBy('id')->lockForUpdate()->get();
            $pembayaran = PembayaranBiayaKapal::whereKey($id)->lockForUpdate()->firstOrFail();
            $hasTemas = $invoices->contains(fn ($invoice) => app(TemasPaymentService::class)->isTemas($invoice));
            if ($hasTemas) {
                throw ValidationException::withMessages(['payment_mode' => 'Pembayaran TEMAS tidak dihubungkan ke COA Transaction.']);
            }
            $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran);

            // 2. Re-run integration
            if (method_exists($this->coaTransactionService, 'pembayaranBiayaKapal')) {
                $this->coaTransactionService->pembayaranBiayaKapal($pembayaran);
            }

            DB::commit();

            return redirect()->route('pembayaran-biaya-kapal.show', $pembayaran->id)
                ->with('success', 'Sinkronisasi COA berhasil.');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sync COA pembayaran biaya kapal: '.$e->getMessage());

            return back()->with('error', 'Gagal sinkronisasi COA: '.$e->getMessage());
        }
    }

    private function generateNomorPembayaran()
    {
        $modul = NomorTerakhir::where('modul', 'PBK')->first();
        $nextNumber = $modul ? $modul->nomor_terakhir + 1 : 1;

        return 'PBK-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
