<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Models\PembayaranPranotaStock;
use App\Models\PranotaStock;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PembayaranPranotaStockController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        // We'll use the same permissions or define new ones if needed,
        // but for now let's assume standard names for stock payments
        $this->middleware('can:pembayaran-pranota-stock-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-stock-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-stock-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-stock-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PembayaranPranotaStock::with(['pranotaStocks', 'createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                    ->orWhere('nomor_accurate', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(20);

        return view('pembayaran-pranota-stock.index', compact('items'));
    }

    public function create(Request $request)
    {
        $pranotaQuery = PranotaStock::where('status', 'approved')
            ->whereDoesntHave('pembayaranPranotaStocks');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaQuery->whereBetween('tanggal_pranota', [$request->start_date, $request->end_date]);
        }

        $pranotaStocks = $pranotaQuery->orderBy('tanggal_pranota', 'desc')->get();
        foreach ($pranotaStocks as $pranota) {
            $pranota->items = $pranota->hydrated_items;
        }

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();
        $akunBiaya = Coa::orderBy('nama_akun')->get(['id', 'nomor_akun', 'nama_akun']);

        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-stock.create', compact('pranotaStocks', 'nomorPembayaran', 'akunCoa', 'akunBiaya'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_stock_ids' => ['required', 'array', 'min:1'],
            'pranota_stock_ids.*' => ['distinct', 'exists:pranota_stocks,id'],
            'nomor_pembayaran' => 'nullable|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['Debit', 'Kredit'])],
            'bank' => 'required|string|max:255',
            'akun_items' => ['required', 'array', 'min:1'],
            'akun_items.*.akun_coa_id' => ['required', 'exists:akun_coa,id'],
            'akun_items.*.nominal' => ['required', 'numeric', 'gt:0'],
            'akun_items.*.keterangan' => ['nullable', 'string', 'max:255'],
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $validated['akun_items'] = array_values($validated['akun_items']);
        $this->validatePaymentAmounts($validated);

        DB::beginTransaction();

        try {
            $modulSis = NomorTerakhir::where('modul', 'SIS')->firstOrCreate(
                ['modul' => 'SIS'],
                ['nomor_terakhir' => 0, 'keterangan' => 'SIS Modul']
            );

            $nomorPembayaran = $this->generateNomorPembayaranSIS();
            $modulSis->increment('nomor_terakhir');

            $paymentData = $validated;
            unset($paymentData['pranota_stock_ids'], $paymentData['akun_items']);
            $paymentData['akun_coa_id'] = $validated['akun_items'][0]['akun_coa_id'];

            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = 'paid';
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            $pembayaran = PembayaranPranotaStock::create($paymentData);
            $pembayaran->akunItems()->createMany($validated['akun_items']);

            foreach ($validated['pranota_stock_ids'] as $pranotaId) {
                $pranota = PranotaStock::findOrFail($pranotaId);

                // Calculate subtotal for this pranota
                $subtotal = 0;
                $hydrated = $pranota->hydrated_items;
                if (is_array($hydrated)) {
                    foreach ($hydrated as $item) {
                        $subtotal += (($item['harga'] ?? 0) * ($item['jumlah'] ?? 0)) + ($item['adjustment'] ?? 0);
                    }
                }
                $subtotal += $pranota->adjustment ?? 0;

                $pembayaran->pranotaStocks()->attach($pranotaId, [
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $pranota->update([
                    'status' => 'paid',
                ]);
            }

            $this->recordAccounting($pembayaran, $validated);

            DB::commit();

            return redirect()->route('pembayaran-pranota-stock.index')
                ->with('success', 'Pembayaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Stock: '.$e->getMessage());

            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        $item = PembayaranPranotaStock::with(['pranotaStocks', 'akunItems'])->findOrFail($id);

        // Items currently in this payment
        $selectedPranotaIds = $item->pranotaStocks->pluck('id')->toArray();

        // Available items (approved and not paid, or already in this payment)
        $pranotaStocks = PranotaStock::where(function ($query) {
            $query->where('status', 'approved')
                ->whereDoesntHave('pembayaranPranotaStocks');
        })
            ->orWhereIn('id', $selectedPranotaIds)
            ->orderBy('tanggal_pranota', 'desc')
            ->get();

        foreach ($pranotaStocks as $pranota) {
            $pranota->items = $pranota->hydrated_items;
        }

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();
        $akunBiaya = Coa::orderBy('nama_akun')->get(['id', 'nomor_akun', 'nama_akun']);

        return view('pembayaran-pranota-stock.edit', compact('item', 'pranotaStocks', 'akunCoa', 'akunBiaya', 'selectedPranotaIds'));
    }

    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranPranotaStock::findOrFail($id);

        $validated = $request->validate([
            'pranota_stock_ids' => ['required', 'array', 'min:1'],
            'pranota_stock_ids.*' => ['distinct', 'exists:pranota_stocks,id'],
            'nomor_pembayaran' => 'required|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['Debit', 'Kredit'])],
            'bank' => 'required|string|max:255',
            'akun_items' => ['required', 'array', 'min:1'],
            'akun_items.*.akun_coa_id' => ['required', 'exists:akun_coa,id'],
            'akun_items.*.nominal' => ['required', 'numeric', 'gt:0'],
            'akun_items.*.keterangan' => ['nullable', 'string', 'max:255'],
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $validated['akun_items'] = array_values($validated['akun_items']);
        $this->validatePaymentAmounts($validated);

        DB::beginTransaction();

        try {
            // Restore old pranota statuses
            foreach ($pembayaran->pranotaStocks as $oldPranota) {
                $oldPranota->update(['status' => 'approved']);
            }

            // Revert accounting
            if (! $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran)) {
                throw new \RuntimeException('Jurnal pembayaran lama tidak dapat dibatalkan.');
            }

            // Update main record
            $paymentData = $validated;
            unset($paymentData['pranota_stock_ids'], $paymentData['akun_items']);
            $paymentData['akun_coa_id'] = $validated['akun_items'][0]['akun_coa_id'];
            $paymentData['updated_by'] = Auth::id();

            $pembayaran->update($paymentData);
            $pembayaran->akunItems()->delete();
            $pembayaran->akunItems()->createMany($validated['akun_items']);

            // Sync pranotas
            $syncData = [];
            foreach ($validated['pranota_stock_ids'] as $pranotaId) {
                $pranota = PranotaStock::findOrFail($pranotaId);

                $subtotal = 0;
                $hydrated = $pranota->hydrated_items;
                if (is_array($hydrated)) {
                    foreach ($hydrated as $i) {
                        $subtotal += (($i['harga'] ?? 0) * ($i['jumlah'] ?? 0)) + ($i['adjustment'] ?? 0);
                    }
                }
                $subtotal += $pranota->adjustment ?? 0;

                $syncData[$pranotaId] = [
                    'subtotal' => $subtotal,
                    'updated_at' => now(),
                ];

                $pranota->update(['status' => 'paid']);
            }

            $pembayaran->pranotaStocks()->sync($syncData);

            $this->recordAccounting($pembayaran, $validated);

            DB::commit();

            return redirect()->route('pembayaran-pranota-stock.index')
                ->with('success', 'Pembayaran berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating Pembayaran Pranota Stock: '.$e->getMessage());

            return back()->withInput()->with('error', 'Gagal update pembayaran: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $item = PembayaranPranotaStock::with(['pranotaStocks.creator', 'createdBy', 'akunCoa', 'akunItems.akunCoa'])->findOrFail($id);

        return view('pembayaran-pranota-stock.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = PembayaranPranotaStock::findOrFail($id);

        DB::beginTransaction();
        try {
            // Restore pranota status
            foreach ($item->pranotaStocks as $pranota) {
                $pranota->update(['status' => 'approved']);
            }

            // Delete accounting entries
            if (! $this->coaTransactionService->deleteTransactionByReference($item->nomor_pembayaran)) {
                throw new \RuntimeException('Jurnal pembayaran tidak dapat dibatalkan.');
            }

            $item->delete();
            DB::commit();

            return redirect()->route('pembayaran-pranota-stock.index')->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus: '.$e->getMessage());
        }
    }

    private function validatePaymentAmounts(array $validated): void
    {
        $pranotas = PranotaStock::whereIn('id', $validated['pranota_stock_ids'])->get();
        $subtotal = $pranotas->sum(function ($pranota) {
            $items = collect($pranota->hydrated_items ?? []);

            return $items->sum(fn ($item) => (float) ($item['harga'] ?? 0) * (float) ($item['jumlah'] ?? 0)
                + (float) ($item['adjustment'] ?? 0)) + (float) ($pranota->adjustment ?? 0);
        });
        $final = $subtotal + (float) ($validated['total_tagihan_penyesuaian'] ?? 0);
        $akunTotal = collect($validated['akun_items'])->sum('nominal');

        if (abs($subtotal - (float) $validated['total_pembayaran']) > 0.01
            || abs($final - (float) $validated['total_tagihan_setelah_penyesuaian']) > 0.01) {
            throw ValidationException::withMessages(['total_pembayaran' => 'Total pembayaran tidak sesuai dengan pranota yang dipilih.']);
        }
        if (abs($akunTotal - $final) > 0.01) {
            throw ValidationException::withMessages(['akun_items' => 'Jumlah nominal seluruh akun harus sama dengan total akhir pembayaran.']);
        }
    }

    private function recordAccounting(PembayaranPranotaStock $pembayaran, array $validated): void
    {
        $debitBank = $validated['jenis_transaksi'] === 'Debit';
        $date = $validated['tanggal_pembayaran'];
        $reference = $pembayaran->nomor_pembayaran;
        $type = 'Pembayaran Pranota Stock';
        $description = 'Pembayaran Pranota Stock - '.$reference;
        $total = (float) $validated['total_tagihan_setelah_penyesuaian'];

        $bankEntry = $this->coaTransactionService->recordTransaction(
            $validated['bank'], $debitBank ? $total : 0, $debitBank ? 0 : $total,
            $date, $reference, $type, $description
        );
        if (! $bankEntry) {
            throw new \RuntimeException('Akun bank/kas tidak ditemukan.');
        }

        foreach ($validated['akun_items'] as $line) {
            $account = Coa::findOrFail($line['akun_coa_id']);
            $amount = (float) $line['nominal'];
            $note = trim((string) ($line['keterangan'] ?? ''));
            $entry = $this->coaTransactionService->recordTransaction(
                $account->nama_akun, $debitBank ? 0 : $amount, $debitBank ? $amount : 0,
                $date, $reference, $type, $note === '' ? $description : $description.' - '.$note,
                $account->id
            );
            if (! $entry) {
                throw new \RuntimeException('Jurnal akun biaya tidak dapat dicatat.');
            }
        }
    }

    private function generateNomorPembayaranSIS()
    {
        $modulSis = NomorTerakhir::firstOrCreate(
            ['modul' => 'SIS'],
            ['nomor_terakhir' => 0, 'keterangan' => 'SIS Modul']
        );

        $now = now();
        $bulan = $now->format('m');
        $tahun = $now->format('y');
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);

        return "SIS-{$bulan}-{$tahun}-{$runningNumber}";
    }

    public function generateNomor()
    {
        return response()->json([
            'success' => true,
            'nomor_pembayaran' => $this->generateNomorPembayaranSIS(),
        ]);
    }
}
