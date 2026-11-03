<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Models\PembayaranPranotaBpjs;
use App\Models\PranotaBpjsHeader;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PembayaranPranotaBpjsController extends Controller
{
    public function __construct(private CoaTransactionService $journal)
    {
        $this->middleware('can:pembayaran-pranota-bpjs-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-bpjs-create')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $pranotas = PranotaBpjsHeader::with('pembayaran.bank')
            ->when($request->filled('search'), fn ($q) => $q->where('nomor_pranota', 'like', '%'.$request->input('search').'%'))
            ->orderByDesc('tanggal_pranota')->orderByDesc('id')->paginate(20)->withQueryString();

        return view('pembayaran-pranota-bpjs.index', compact('pranotas'));
    }

    public function create(Request $request)
    {
        if (! $request->filled('pranota_id')) {
            return redirect()->route('pembayaran-pranota-bpjs.index');
        }
        $pranota = PranotaBpjsHeader::findOrFail($request->integer('pranota_id'));
        abort_unless(in_array($pranota->status, ['draft', 'approved', 'unpaid', 'partial']), 422, 'Pranota tidak dapat dibayar.');
        $groups = (new \App\Services\BpjsPaymentParts)->groups($pranota);
        $accounts = Coa::orderBy('nomor_akun')->get();

        return view('pembayaran-pranota-bpjs.create', compact('pranota', 'groups', 'accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pranota_ids' => ['required', 'array', 'min:1'],
            'group_keys' => ['sometimes', 'required', 'array', 'min:1'],
            'group_keys.*' => ['required', 'string', 'distinct'],
            'pranota_ids.*' => ['required', 'integer', 'distinct', 'exists:pranota_bpjs_headers,id'],
            'tanggal_pembayaran' => ['required', 'date'],
            'bank_coa_id' => ['required', 'integer', Rule::exists('akun_coa', 'id')],
            'akun_coa_id' => ['required', 'integer', 'different:bank_coa_id', Rule::exists('akun_coa', 'id')],
            'nomor_accurate' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $payment = DB::transaction(function () use ($data) {
                $pranotas = PranotaBpjsHeader::whereIn('id', $data['pranota_ids'])->orderBy('id')->lockForUpdate()->get();
                if ($pranotas->count() !== count($data['pranota_ids'])) {
                    throw ValidationException::withMessages(['pranota_ids' => 'Pranota tidak ditemukan.']);
                }
                foreach ($pranotas as $pranota) {
                    if (! in_array($pranota->status, ['draft', 'approved', 'unpaid', 'partial']) || ($pranota->pembayaran()->exists() && ! isset($data['group_keys'])) || $pranota->grand_total <= 0) {
                        throw ValidationException::withMessages(['pranota_ids' => 'Pranota '.$pranota->nomor_pranota.' sudah dibayar, dibatalkan, atau tidak memiliki tagihan.']);
                    }
                    if ($data['tanggal_pembayaran'] < $pranota->tanggal_pranota->format('Y-m-d')) {
                        throw ValidationException::withMessages(['tanggal_pembayaran' => 'Tanggal pembayaran tidak boleh sebelum tanggal pranota.']);
                    }
                }
                $allocations = [];
                foreach ($pranotas as $pranota) {
                    if (isset($data['group_keys'])) {
                        if ($pranotas->count() !== 1) {
                            throw ValidationException::withMessages(['group_keys' => 'Pilih satu pranota untuk pembayaran terpisah.']);
                        }
                        $available = (new \App\Services\BpjsPaymentParts)->groups($pranota);
                        $selected = array_intersect_key($available, array_flip($data['group_keys']));
                        if (count($selected) !== count($data['group_keys'])) {
                            throw ValidationException::withMessages(['group_keys' => 'Tipe sudah dibayar atau tidak tersedia. Muat ulang halaman.']);
                        }
                        $parts = array_merge(...array_column(array_values($selected), 'parts'));
                        $subtotal = round(array_sum(array_column($parts, 'subtotal')), 2);
                        $paid = round((float) $pranota->pembayaran()->sum('pembayaran_pranota_bpjs_items.subtotal'), 2);
                        if ($subtotal <= 0 || $paid + $subtotal > round((float) $pranota->grand_total, 2)) {
                            throw ValidationException::withMessages(['group_keys' => 'Total bagian tidak sesuai sisa tagihan pranota.']);
                        }
                        $allocations[$pranota->id] = compact('subtotal', 'parts', 'paid');
                    } else {
                        $allocations[$pranota->id] = ['subtotal' => (float) $pranota->grand_total, 'parts' => [], 'paid' => 0];
                    }
                }
                // Lock accounts in a consistent order so concurrent payments preserve balances.
                $accounts = Coa::whereIn('id', [$data['bank_coa_id'], $data['akun_coa_id']])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                $bank = $accounts->get($data['bank_coa_id']);
                $account = $accounts->get($data['akun_coa_id']);
                if (! $bank || ! $account) {
                    throw ValidationException::withMessages(['bank_coa_id' => 'Pilih akun COA yang valid.']);
                }
                NomorTerakhir::firstOrCreate(['modul' => 'SIS'], ['nomor_terakhir' => 0, 'keterangan' => 'Nomor Pembayaran']);
                $sequence = NomorTerakhir::where('modul', 'SIS')->lockForUpdate()->firstOrFail();
                $sequence->increment('nomor_terakhir');
                $number = 'SIS-'.now()->format('m-y').'-'.str_pad($sequence->nomor_terakhir, 6, '0', STR_PAD_LEFT);
                $total = round(array_sum(array_column($allocations, 'subtotal')), 2);
                $payment = PembayaranPranotaBpjs::create([
                    'nomor_pembayaran' => $number, 'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                    'bank_coa_id' => $bank->id, 'akun_coa_id' => $account->id,
                    'total_pembayaran' => $total, 'nomor_accurate' => $data['nomor_accurate'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null, 'created_by' => auth()->id(),
                ]);
                foreach ($pranotas as $pranota) {
                    $allocation = $allocations[$pranota->id];
                    $payment->pranotas()->attach($pranota->id, ['subtotal' => $allocation['subtotal']]);
                    foreach ($allocation['parts'] as $part) {
                        DB::table('pembayaran_pranota_bpjs_parts')->insert($part + ['pembayaran_pranota_bpjs_id' => $payment->id]);
                    }
                    $settled = round($allocation['paid'] + $allocation['subtotal'], 2) >= round((float) $pranota->grand_total, 2);
                    $pranota->update(['status' => $settled ? 'paid' : 'partial', 'updated_by' => auth()->id()]);
                }
                if (! $this->journal->recordDoubleEntry(
                    ['coa_id' => $account->id, 'nama_akun' => $account->nama_akun, 'jumlah' => $total],
                    ['coa_id' => $bank->id, 'nama_akun' => $bank->nama_akun, 'jumlah' => $total],
                    $data['tanggal_pembayaran'], $number, 'Pembayaran Pranota BPJS', 'Pembayaran Pranota BPJS - '.$number
                )) {
                    throw new \RuntimeException('Jurnal pembayaran gagal dicatat.');
                }

                return $payment;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan pembayaran pranota BPJS', ['exception' => $e]);

            return back()->withInput()->with('error', 'Pembayaran gagal disimpan. Silakan coba kembali.');
        }

        return redirect()->route('pembayaran-pranota-bpjs.index')->with('success', 'Pembayaran '.$payment->nomor_pembayaran.' berhasil disimpan.');
    }

    public function show(PembayaranPranotaBpjs $pembayaran_pranota_bpj)
    {
        $payment = $pembayaran_pranota_bpj->load(['pranotas', 'bank', 'akunCoa', 'createdBy']);

        $parts = DB::table('pembayaran_pranota_bpjs_parts')->where('pembayaran_pranota_bpjs_id', $payment->id)
            ->selectRaw('komponen, tipe, SUM(subtotal) as subtotal')->groupBy('komponen', 'tipe')->get();

        return view('pembayaran-pranota-bpjs.show', compact('payment', 'parts'));
    }
}
