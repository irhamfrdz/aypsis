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

    private function bankQuery()
    {
        return Coa::where(function ($query) {
            $query->where('tipe_akun', 'like', '%bank%')
                ->orWhere('nama_akun', 'like', '%bank%')
                ->orWhere('nama_akun', 'like', '%kas%');
        });
    }

    public function index(Request $request)
    {
        $payments = PembayaranPranotaBpjs::with('bank')->when($request->filled('search'), function ($query) use ($request) {
            $search = '%'.$request->input('search').'%';
            $query->where(function ($query) use ($search) {
                $query->where('nomor_pembayaran', 'like', $search)
                    ->orWhere('nomor_accurate', 'like', $search)
                    ->orWhereHas('pranotas', fn ($q) => $q->where('nomor_pranota', 'like', $search));
            });
        })->orderByDesc('id')->paginate(20)->withQueryString();

        return view('pembayaran-pranota-bpjs.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $pranotas = PranotaBpjsHeader::whereIn('status', ['draft', 'approved', 'unpaid'])
            ->where('grand_total', '>', 0)->whereDoesntHave('pembayaran')
            ->orderByDesc('tanggal_pranota')->get();
        $banks = $this->bankQuery()->orderBy('nama_akun')->get();
        $accounts = Coa::orderBy('nomor_akun')->get();

        return view('pembayaran-pranota-bpjs.create', compact('pranotas', 'banks', 'accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pranota_ids' => ['required', 'array', 'min:1'],
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
                    if (! in_array($pranota->status, ['draft', 'approved', 'unpaid']) || $pranota->pembayaran()->exists() || $pranota->grand_total <= 0) {
                        throw ValidationException::withMessages(['pranota_ids' => 'Pranota '.$pranota->nomor_pranota.' sudah dibayar, dibatalkan, atau tidak memiliki tagihan.']);
                    }
                    if ($data['tanggal_pembayaran'] < $pranota->tanggal_pranota->format('Y-m-d')) {
                        throw ValidationException::withMessages(['tanggal_pembayaran' => 'Tanggal pembayaran tidak boleh sebelum tanggal pranota.']);
                    }
                }
                // Lock accounts in a consistent order so concurrent payments preserve balances.
                $accounts = Coa::whereIn('id', [$data['bank_coa_id'], $data['akun_coa_id']])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                $bank = $accounts->get($data['bank_coa_id']);
                $account = $accounts->get($data['akun_coa_id']);
                if (! $bank || ! $account || ! $this->bankQuery()->whereKey($bank->id)->exists()) {
                    throw ValidationException::withMessages(['bank_coa_id' => 'Pilih akun kas/bank yang valid.']);
                }
                NomorTerakhir::firstOrCreate(['modul' => 'SIS'], ['nomor_terakhir' => 0, 'keterangan' => 'Nomor Pembayaran']);
                $sequence = NomorTerakhir::where('modul', 'SIS')->lockForUpdate()->firstOrFail();
                $sequence->increment('nomor_terakhir');
                $number = 'SIS-'.now()->format('m-y').'-'.str_pad($sequence->nomor_terakhir, 6, '0', STR_PAD_LEFT);
                $total = round($pranotas->sum('grand_total'), 2);
                $payment = PembayaranPranotaBpjs::create([
                    'nomor_pembayaran' => $number, 'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                    'bank_coa_id' => $bank->id, 'akun_coa_id' => $account->id,
                    'total_pembayaran' => $total, 'nomor_accurate' => $data['nomor_accurate'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null, 'created_by' => auth()->id(),
                ]);
                foreach ($pranotas as $pranota) {
                    $payment->pranotas()->attach($pranota->id, ['subtotal' => $pranota->grand_total]);
                    $pranota->update(['status' => 'paid', 'updated_by' => auth()->id()]);
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

        return view('pembayaran-pranota-bpjs.show', compact('payment'));
    }
}
