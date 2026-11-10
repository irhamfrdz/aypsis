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
        $this->middleware('can:pembayaran-pranota-bpjs-create')->only(['create', 'store', 'update', 'destroy']);
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
            'group_keys' => ['required_with:select_employees,employee_keys,bpjs_group', 'array', 'min:1'],
            'group_keys.*' => ['required', 'string', 'distinct'],
            'bpjs_group' => ['sometimes', 'required', 'string'],
            'select_employees' => ['sometimes', 'in:1'],
            'employee_keys' => ['required_if:select_employees,1', 'array', 'min:1'],
            'employee_keys.*' => ['required', 'string', 'distinct', 'regex:/^[1-9][0-9]*:(jkn|jamsostek)$/'],
            'pranota_ids.*' => ['required', 'integer', 'distinct', 'exists:pranota_bpjs_headers,id'],
            'tanggal_pembayaran' => ['required', 'date'],
            'bank_coa_id' => ['required_without:group_keys', 'nullable', 'integer', Rule::exists('akun_coa', 'id')],
            'akun_coa_id' => ['required', 'integer', Rule::exists('akun_coa', 'id')],
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
                }
                $allocations = [];
                $creditAmounts = [];
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
                        if (isset($data['bpjs_group']) && collect($selected)->contains(fn ($group) => $group['selection_key'] !== $data['bpjs_group'])) {
                            throw ValidationException::withMessages(['group_keys' => 'Karyawan harus berasal dari Group BPJS yang dipilih.']);
                        }
                        if (isset($data['employee_keys'])) {
                            $wanted = array_flip($data['employee_keys']);
                            $matched = 0;
                            foreach ($selected as $key => &$group) {
                                $group['parts'] = array_values(array_filter($group['parts'], fn ($part) => isset($wanted[$part['pranota_bpjs_detail_id'].':'.$part['komponen']])));
                                $matched += count($group['parts']);
                                $group['amount'] = round(array_sum(array_column($group['parts'], 'subtotal')), 2);
                            }
                            unset($group);
                            if ($matched !== count($data['employee_keys'])) {
                                throw ValidationException::withMessages(['employee_keys' => 'Ada karyawan yang sudah dibayar atau tidak sesuai group dan tipe yang dipilih. Muat ulang halaman.']);
                            }
                            $selected = array_filter($selected, fn ($group) => count($group['parts']) > 0);
                        }
                        $creditAmounts = (new \App\Services\BpjsJournalAccount)->selectedAmounts(array_values($selected));
                        $parts = array_merge(...array_column(array_values($selected), 'parts'));
                        $detailMap = $pranota->details->keyBy('id');
                        $amountResolver = new \App\Services\BpjsJournalAmounts;
                        foreach ($parts as &$part) {
                            [$part['tunjangan'], $part['biaya'], $part['hutang']] = $amountResolver->forDetail($detailMap->get($part['pranota_bpjs_detail_id']), $part['komponen']);
                        }
                        unset($part);
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
                $total = round(array_sum(array_column($allocations, 'subtotal')), 2);
                if (! isset($data['group_keys'])) {
                    $creditAmounts = [(int) $data['bank_coa_id'] => $total];
                }
                if (round(array_sum($creditAmounts), 2) !== $total) {
                    throw ValidationException::withMessages(['group_keys' => 'Total alokasi akun jurnal tidak sesuai total pembayaran.']);
                }
                if (array_key_exists((int) $data['akun_coa_id'], $creditAmounts)) {
                    throw ValidationException::withMessages(['akun_coa_id' => 'Bank yang digunakan harus berbeda dengan Akun Bank dari jurnal.']);
                }
                // Lock accounts in a consistent order so concurrent payments preserve balances.
                $accounts = Coa::whereIn('id', [...array_keys($creditAmounts), $data['akun_coa_id']])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                $bank = $accounts->get(array_key_first($creditAmounts));
                $account = $accounts->get($data['akun_coa_id']);
                if (! $bank || ! $account || $accounts->count() !== count($creditAmounts) + 1) {
                    throw ValidationException::withMessages(['bank_coa_id' => 'Pilih akun COA yang valid.']);
                }
                NomorTerakhir::firstOrCreate(['modul' => 'SIS'], ['nomor_terakhir' => 0, 'keterangan' => 'Nomor Pembayaran']);
                $sequence = NomorTerakhir::where('modul', 'SIS')->lockForUpdate()->firstOrFail();
                $sequence->increment('nomor_terakhir');
                $number = 'SIS-'.now()->format('m-y').'-'.str_pad($sequence->nomor_terakhir, 6, '0', STR_PAD_LEFT);
                $bankAllocations = [];
                foreach ($creditAmounts as $coaId => $amount) {
                    $coa = $accounts->get($coaId);
                    $bankAllocations[] = ['coa_id' => $coa->id, 'nomor_akun' => $coa->nomor_akun, 'nama_akun' => $coa->nama_akun, 'subtotal' => $amount];
                }
                $payment = PembayaranPranotaBpjs::create([
                    'nomor_pembayaran' => $number, 'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                    'bank_coa_id' => $bank->id, 'akun_coa_id' => $account->id,
                    'total_pembayaran' => $total, 'nomor_accurate' => $data['nomor_accurate'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null, 'created_by' => auth()->id(),
                    'bank_allocations' => $bankAllocations,
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
                foreach ($bankAllocations as $allocation) {
                    if (! $this->journal->recordDoubleEntry(
                        ['coa_id' => $allocation['coa_id'], 'nama_akun' => $allocation['nama_akun'], 'jumlah' => $allocation['subtotal']],
                        ['coa_id' => $account->id, 'nama_akun' => $account->nama_akun, 'jumlah' => $allocation['subtotal']],
                        $data['tanggal_pembayaran'], $number, 'Pembayaran Pranota BPJS', 'Pembayaran Pranota BPJS - '.$number
                    )) {
                        throw new \RuntimeException('Jurnal pembayaran gagal dicatat.');
                    }
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

    public function destroy(PembayaranPranotaBpjs $pembayaran_pranota_bpj)
    {
        try {
            DB::transaction(function () use ($pembayaran_pranota_bpj) {
                $ids = $pembayaran_pranota_bpj->pranotas()->pluck('pranota_bpjs_headers.id');
                $pranotas = PranotaBpjsHeader::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
                $payment = PembayaranPranotaBpjs::whereKey($pembayaran_pranota_bpj->id)->lockForUpdate()->firstOrFail();
                $journals = \App\Models\CoaTransaction::where('nomor_referensi', $payment->nomor_pembayaran)
                    ->where('jenis_transaksi', 'Pembayaran Pranota BPJS')->lockForUpdate()->get();
                $expectedDebits = collect($payment->bank_allocations ?: [['coa_id' => $payment->bank_coa_id, 'subtotal' => $payment->total_pembayaran]])
                    ->groupBy('coa_id')->map(fn ($rows) => round($rows->sum('subtotal'), 2));
                $byAccount = $journals->groupBy('coa_id');
                $accounts = Coa::whereIn('id', $byAccount->keys())->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                // Require both sides of the original payment before changing any balance.
                if ($accounts->count() !== $byAccount->count()
                    || round($journals->sum('debit'), 2) !== round((float) $payment->total_pembayaran, 2)
                    || round($journals->sum('kredit'), 2) !== round((float) $payment->total_pembayaran, 2)
                    || round($byAccount->get($payment->akun_coa_id, collect())->sum('kredit'), 2) !== round((float) $payment->total_pembayaran, 2)) {
                    throw new \RuntimeException('Jurnal kedua sisi pembayaran tidak lengkap.');
                }
                foreach ($expectedDebits as $coaId => $amount) {
                    if (round($byAccount->get($coaId, collect())->sum('debit'), 2) !== $amount) {
                        throw new \RuntimeException('Jurnal Akun Bank tidak sesuai pembayaran.');
                    }
                }
                foreach ($byAccount as $coaId => $entries) {
                    $account = $accounts->get($coaId);
                    $account->saldo = round((float) $account->saldo - $entries->sum('debit') + $entries->sum('kredit'), 2);
                    $account->save();
                }
                foreach ($journals as $entry) {
                    $entry->delete();
                }
                DB::table('pembayaran_pranota_bpjs_parts')->where('pembayaran_pranota_bpjs_id', $payment->id)->delete();
                $payment->pranotas()->detach();
                $payment->delete();
                foreach ($pranotas as $pranota) {
                    $paid = round((float) $pranota->pembayaran()->sum('pembayaran_pranota_bpjs_items.subtotal'), 2);
                    $pranota->update(['status' => $paid <= 0 ? 'unpaid' : ($paid >= round((float) $pranota->grand_total, 2) ? 'paid' : 'partial'), 'updated_by' => auth()->id()]);
                }
                Log::info('Pembayaran pranota BPJS ditarik', ['nomor_pembayaran' => $payment->nomor_pembayaran, 'user_id' => auth()->id()]);
            });
        } catch (\Exception $e) {
            Log::error('Gagal menarik pembayaran pranota BPJS', ['exception' => $e]);

            return back()->with('error', 'Pembayaran gagal ditarik. Silakan muat ulang dan coba kembali.');
        }

        return redirect()->route('pembayaran-pranota-bpjs.index')->with('success', 'Pembayaran berhasil ditarik. Sisa tagihan telah diperbarui dan dapat dibayar kembali.');
    }

    public function update(Request $request, PembayaranPranotaBpjs $pembayaran_pranota_bpj)
    {
        $data = $request->validate(['nomor_accurate' => ['nullable', 'string', 'max:255']]);
        $pembayaran_pranota_bpj->update(['nomor_accurate' => $data['nomor_accurate'] ?? null]);

        return redirect()->route('pembayaran-pranota-bpjs.show', $pembayaran_pranota_bpj)->with('success', 'Nomor Accurate berhasil disimpan.');
    }

    public function show(PembayaranPranotaBpjs $pembayaran_pranota_bpj)
    {
        $payment = $pembayaran_pranota_bpj->load(['pranotas', 'bank', 'akunCoa', 'createdBy']);

        $parts = DB::table('pembayaran_pranota_bpjs_parts')->where('pembayaran_pranota_bpjs_id', $payment->id)
            ->selectRaw('komponen, tipe, SUM(subtotal) as subtotal')->groupBy('komponen', 'tipe')->get();

        $journalAmounts = [];
        $amountResolver = new \App\Services\BpjsJournalAmounts;
        $paidParts = DB::table('pembayaran_pranota_bpjs_parts')
            ->where('pembayaran_pranota_bpjs_id', $payment->id)
            ->join('pranota_bpjs_details', 'pranota_bpjs_details.id', '=', 'pembayaran_pranota_bpjs_parts.pranota_bpjs_detail_id')
            ->select('pembayaran_pranota_bpjs_parts.komponen', 'pembayaran_pranota_bpjs_parts.tipe', 'pranota_bpjs_details.*')->get();
        foreach ($paidParts as $paidPart) {
            $detail = new \App\Models\PranotaBpjsDetail((array) $paidPart);
            [$tunjangan, $biaya, $hutang] = $amountResolver->forDetail($detail, $paidPart->komponen);
            $key = $paidPart->komponen.'|'.$paidPart->tipe;
            $journalAmounts[$key] ??= ['tunjangan' => 0, 'biaya' => 0, 'hutang' => 0];
            $journalAmounts[$key]['tunjangan'] += $tunjangan;
            $journalAmounts[$key]['biaya'] += $biaya;
            $journalAmounts[$key]['hutang'] += $hutang;
        }

        return view('pembayaran-pranota-bpjs.show', compact('payment', 'parts', 'journalAmounts'));
    }

    public function print(PembayaranPranotaBpjs $pembayaran_pranota_bpj)
    {
        $payment = $pembayaran_pranota_bpj->load(['pranotas', 'bank', 'akunCoa', 'createdBy']);
        $parts = DB::table('pembayaran_pranota_bpjs_parts')
            ->where('pembayaran_pranota_bpjs_id', $payment->id)
            ->selectRaw('komponen, tipe, SUM(subtotal) as subtotal, SUM(tunjangan) as tunjangan, SUM(biaya) as biaya, SUM(hutang) as hutang')
            ->groupBy('komponen', 'tipe')->get();

        return view('pembayaran-pranota-bpjs.print', compact('payment', 'parts'));
    }
}
