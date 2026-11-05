<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\InvoiceKasPinang;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvoiceKasPinangController extends Controller
{
    public function __construct(private CoaTransactionService $coaTransactionService) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $invoices = InvoiceKasPinang::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('nomor_invoice', 'like', "%{$search}%")
                    ->orWhere('pihak_terkait', 'like', "%{$search}%")
                    ->orWhere('referensi', 'like', "%{$search}%");
            }))
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('invoice-kas-pinang.index', compact('invoices', 'search'));
    }

    public function create()
    {
        return view('invoice-kas-pinang.form', [
            'invoice' => new InvoiceKasPinang,
            'akunKas' => $this->cashAccounts(),
            'akunLawan' => Coa::orderBy('nomor_akun')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $items = $this->calculateItems($data['items']);
        $this->validateTotal($data, $items);
        unset($data['items']);

        $invoice = DB::transaction(function () use ($data, $items) {
            $invoice = InvoiceKasPinang::create([
                ...$data,
                'nomor_invoice' => (string) Str::uuid(),
                'total' => array_sum(array_column($items, 'subtotal')),
                'created_by' => Auth::id(),
            ]);
            $invoice->update([
                'nomor_invoice' => 'IKP-'.date('Ym', strtotime($data['tanggal_transaksi'])).'-'.str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
            ]);
            $invoice->items()->createMany($items);
            if ($invoice->status === 'terbit') {
                $this->postJournal($invoice);
            }

            return $invoice;
        });

        return redirect()->route('invoice-kas-pinang.print', $invoice)->with('success', 'Invoice Kas Pinang berhasil dibuat.');
    }

    public function edit(InvoiceKasPinang $invoiceKasPinang)
    {
        $invoiceKasPinang->load('items');

        return view('invoice-kas-pinang.form', [
            'invoice' => $invoiceKasPinang,
            'akunKas' => $this->cashAccounts(),
            'akunLawan' => Coa::orderBy('nomor_akun')->get(),
        ]);
    }

    public function update(Request $request, InvoiceKasPinang $invoiceKasPinang)
    {
        $data = $this->validated($request);
        $items = $this->calculateItems($data['items']);
        $this->validateTotal($data, $items);
        unset($data['items']);

        DB::transaction(function () use ($invoiceKasPinang, $data, $items) {
            $invoice = InvoiceKasPinang::whereKey($invoiceKasPinang->id)->lockForUpdate()->firstOrFail();
            $this->lockAccounts([$invoice->akun_kas_id, $invoice->akun_lawan_id, $data['akun_kas_id'], $data['akun_lawan_id']]);
            if ($invoice->status === 'terbit') {
                $this->reverseJournal($invoice);
            }
            $invoice->update([
                ...$data,
                'total' => array_sum(array_column($items, 'subtotal')),
            ]);
            $invoice->items()->delete();
            $invoice->items()->createMany($items);
            if ($invoice->status === 'terbit') {
                $this->postJournal($invoice);
            }
        });

        return redirect()->route('invoice-kas-pinang.index')->with('success', 'Invoice Kas Pinang berhasil diperbarui.');
    }

    public function destroy(InvoiceKasPinang $invoiceKasPinang)
    {
        DB::transaction(function () use ($invoiceKasPinang) {
            $invoice = InvoiceKasPinang::whereKey($invoiceKasPinang->id)->lockForUpdate()->firstOrFail();
            $this->lockAccounts([$invoice->akun_kas_id, $invoice->akun_lawan_id]);
            if ($invoice->status === 'terbit') {
                $this->reverseJournal($invoice);
            }
            $invoice->delete();
        });

        return redirect()->route('invoice-kas-pinang.index')->with('success', 'Invoice Kas Pinang berhasil dihapus.');
    }

    public function print(InvoiceKasPinang $invoiceKasPinang)
    {
        $invoiceKasPinang->load('items', 'akunKas', 'akunLawan');

        return view('invoice-kas-pinang.print', ['invoice' => $invoiceKasPinang]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'tanggal_transaksi' => ['required', 'date'],
            'arus_kas' => ['required', 'in:masuk,keluar'],
            'akun_kas_id' => ['required', 'integer', 'exists:akun_coa,id'],
            'akun_lawan_id' => ['required', 'integer', 'exists:akun_coa,id', 'different:akun_kas_id'],
            'pihak_terkait' => ['nullable', 'string', 'max:255'],
            'referensi' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,terbit'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.uraian' => ['required', 'string', 'max:255'],
            'items.*.jumlah' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99', 'decimal:0,2'],
            'items.*.satuan' => ['nullable', 'string', 'max:50'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0', 'max:999999999999.99', 'decimal:0,2'],
        ]);

        if (! $this->cashAccounts()->contains('id', (int) $data['akun_kas_id'])) {
            throw ValidationException::withMessages(['akun_kas_id' => 'Pilih akun Kas/Bank bertipe Aset.']);
        }

        return $data;
    }

    private function calculateItems(array $items): array
    {
        $calculated = array_map(function (array $item) {
            $subtotal = round((float) $item['jumlah'] * (float) $item['harga_satuan'], 2);
            if (! is_finite($subtotal) || $subtotal > 9999999999999.99) {
                throw ValidationException::withMessages(['items' => 'Subtotal rincian melebihi batas nominal.']);
            }
            $item['subtotal'] = $subtotal;

            return $item;
        }, $items);

        if (array_sum(array_column($calculated, 'subtotal')) > 9999999999999.99) {
            throw ValidationException::withMessages(['items' => 'Total transaksi melebihi batas nominal.']);
        }

        return $calculated;
    }

    private function cashAccounts()
    {
        return Coa::where('tipe_akun', 'Aset')
            ->where(function ($query) {
                $query->where('nama_akun', 'like', '%kas%')
                    ->orWhere('nama_akun', 'like', '%bank%')
                    ->orWhere('nama_akun', 'like', '%cash%');
            })
            ->orderBy('nomor_akun')
            ->get();
    }

    private function validateTotal(array $data, array $items): void
    {
        if ($data['status'] === 'terbit' && array_sum(array_column($items, 'subtotal')) <= 0) {
            throw ValidationException::withMessages(['items' => 'Invoice terbit harus memiliki total lebih dari nol.']);
        }
    }

    private function lockAccounts(array $ids): void
    {
        Coa::whereIn('id', array_unique($ids))->orderBy('id')->lockForUpdate()->get();
    }

    private function postJournal(InvoiceKasPinang $invoice): void
    {
        $this->lockAccounts([$invoice->akun_kas_id, $invoice->akun_lawan_id]);
        $kas = Coa::findOrFail($invoice->akun_kas_id);
        $lawan = Coa::findOrFail($invoice->akun_lawan_id);
        if ($kas->tipe_akun !== 'Aset' || ! preg_match('/kas|bank|cash/i', $kas->nama_akun)) {
            throw ValidationException::withMessages(['akun_kas_id' => 'Pilih akun Kas/Bank bertipe Aset.']);
        }

        $jumlah = (float) $invoice->total;
        $debit = $invoice->arus_kas === 'masuk' ? $kas : $lawan;
        $kredit = $invoice->arus_kas === 'masuk' ? $lawan : $kas;
        if (! $this->coaTransactionService->recordDoubleEntry(
            ['nama_akun' => $debit->nama_akun, 'coa_id' => $debit->id, 'jumlah' => $jumlah],
            ['nama_akun' => $kredit->nama_akun, 'coa_id' => $kredit->id, 'jumlah' => $jumlah],
            $invoice->tanggal_transaksi->format('Y-m-d'),
            $invoice->nomor_invoice,
            'invoice_kas_pinang',
            'Invoice Kas Pinang '.ucfirst($invoice->arus_kas).' - '.($invoice->catatan ?: $invoice->pihak_terkait)
        )) {
            throw new \RuntimeException('Jurnal COA Invoice Kas Pinang gagal dicatat.');
        }
    }

    private function reverseJournal(InvoiceKasPinang $invoice): void
    {
        $count = CoaTransaction::where('nomor_referensi', $invoice->nomor_invoice)->count();
        if ($count !== 2 || ! $this->coaTransactionService->deleteTransactionByReference($invoice->nomor_invoice)) {
            throw new \RuntimeException('Jurnal COA Invoice Kas Pinang tidak lengkap; perubahan dibatalkan.');
        }
    }
}
