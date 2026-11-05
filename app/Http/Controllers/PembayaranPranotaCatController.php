<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\PembayaranPranotaCat;
use App\Models\PembayaranPranotaCatItem;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PembayaranPranotaCatController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $pembayaranList = PembayaranPranotaCat::with(['pranotaTagihanCats', 'pranotaPerbaikanKontainers'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%'.$search.'%';

                $query->where(function ($query) use ($term) {
                    $query->where('nomor_pembayaran', 'like', $term)
                        ->orWhere('nomor_accurate', 'like', $term)
                        ->orWhere('bank', 'like', $term)
                        ->orWhereHas('pranotaTagihanCats', function ($query) use ($term) {
                            $query->where('no_invoice', 'like', $term);
                        })
                        ->orWhereHas('pranotaPerbaikanKontainers', function ($query) use ($term) {
                            $query->where('nomor_pranota', 'like', $term);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('pembayaran-pranota-cat.index', compact('pembayaranList', 'search'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranPranotaCat::with(['pranotaTagihanCats', 'pranotaPerbaikanKontainers', 'akunCoa', 'akunItems.akunCoa'])->findOrFail($id);

        return view('pembayaran-pranota-cat.show', compact('pembayaran'));
    }

    /**
     * Show form to select pranota CAT for payment
     */
    public function create(Request $request)
    {
        // Check permission manually to provide better error message
        if (! Gate::allows('pembayaran-pranota-cat-create')) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota CAT. Silakan hubungi administrator.');
        }

        // If pranota_ids or pranota_tagihan_ids are provided (from pranota index page), redirect to payment form
        if (($request->has('pranota_ids') && ! empty($request->pranota_ids)) || ($request->has('pranota_tagihan_ids') && ! empty($request->pranota_tagihan_ids))) {
            return $this->showPaymentForm($request);
        }

        // Clear any old validation errors from session for fresh form load
        if ($request->isMethod('get')) {
            session()->forget('errors');
        }

        // Get all approved PranotaPerbaikanKontainer that are unpaid/unlinked
        $pranotaList = \App\Models\PranotaPerbaikanKontainer::where('status', 'approved')
            ->whereNotIn('id', function ($query) {
                $query->select('pranota_perbaikan_kontainer_id')
                    ->from('pembayaran_pranota_cat_items')
                    ->whereNotNull('pranota_perbaikan_kontainer_id');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($pranota) {
                return $pranota->calculateTotalCatAmount() > 0;
            });

        // Get all unpaid PranotaTagihanCat
        $pranotaTagihanCatList = \App\Models\PranotaTagihanCat::where('status', 'unpaid')
            ->whereNotIn('id', function ($query) {
                $query->select('pranota_tagihan_cat_id')
                    ->from('pembayaran_pranota_cat_items')
                    ->whereNotNull('pranota_tagihan_cat_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get Bank/Kas accounts only, sorted by account number
        $akunCoa = Coa::where(function ($query) {
            $query->where('tipe_akun', 'Kas/Bank')
                ->orWhere('tipe_akun', 'Bank/Kas')
                ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                ->orWhere('tipe_akun', 'LIKE', '%Bank%');
        })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        $akunBiaya = Coa::orderBy('nomor_akun')->get(['id', 'nomor_akun', 'nama_akun']);

        return view('pembayaran-pranota-cat.create', compact('pranotaList', 'pranotaTagihanCatList', 'akunCoa', 'akunBiaya'));
    }

    /**
     * Show payment form for selected pranota CAT
     */
    public function showPaymentForm(Request $request)
    {
        $request->validate([
            'pranota_ids' => 'nullable|array',
            'pranota_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
            'pranota_tagihan_ids' => 'nullable|array',
            'pranota_tagihan_ids.*' => 'exists:pranota_tagihan_cat,id',
        ]);

        $pranotaIds = $request->input('pranota_ids', []);
        $pranotaTagihanIds = $request->input('pranota_tagihan_ids', []);

        if (empty($pranotaIds) && empty($pranotaTagihanIds)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu pranota CAT atau perbaikan untuk dibayar.');
        }

        $pranotaList = \App\Models\PranotaPerbaikanKontainer::whereIn('id', $pranotaIds)->get();
        $pranotaTagihanCatList = \App\Models\PranotaTagihanCat::whereIn('id', $pranotaTagihanIds)->get();

        // Validate that all selected perbaikan are approved and not paid yet
        foreach ($pranotaList as $pranota) {
            if ($pranota->status !== 'approved') {
                return redirect()->back()->with('error', "Pranota {$pranota->nomor_pranota} statusnya bukan approved atau tidak dapat diproses");
            }
            $isPaid = PembayaranPranotaCatItem::where('pranota_perbaikan_kontainer_id', $pranota->id)->exists();
            if ($isPaid) {
                return redirect()->back()->with('error', "Pranota {$pranota->nomor_pranota} sudah dibayar");
            }
        }

        // Validate that all selected tagihan are unpaid and not paid yet
        foreach ($pranotaTagihanCatList as $pranota) {
            if ($pranota->status !== 'unpaid') {
                return redirect()->back()->with('error', "Pranota Tagihan {$pranota->no_invoice} statusnya bukan unpaid atau tidak dapat diproses");
            }
            $isPaid = PembayaranPranotaCatItem::where('pranota_tagihan_cat_id', $pranota->id)->exists();
            if ($isPaid) {
                return redirect()->back()->with('error', "Pranota Tagihan {$pranota->no_invoice} sudah dibayar");
            }
        }

        // Generate nomor pembayaran
        $nomorPembayaran = $request->input('nomor_pembayaran', '');
        if (empty($nomorPembayaran)) {
            $nomorPembayaran = PembayaranPranotaCat::generateNomorPembayaran();
        }

        $totalPembayaran = $pranotaList->sum(function ($pranota) {
            return $pranota->calculateTotalCatAmount();
        }) + $pranotaTagihanCatList->sum(function ($pranota) {
            return $pranota->total_amount;
        });

        // Get Bank/Kas accounts only, sorted by account number
        $akunCoa = Coa::where(function ($query) {
            $query->where('tipe_akun', 'Kas/Bank')
                ->orWhere('tipe_akun', 'Bank/Kas')
                ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                ->orWhere('tipe_akun', 'LIKE', '%Bank%');
        })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        $akunBiaya = Coa::orderBy('nomor_akun')->get(['id', 'nomor_akun', 'nama_akun']);

        $catPaymentCounter = PembayaranPranotaCat::count() + 1;

        return view('pembayaran-pranota-cat.payment-form', compact('pranotaList', 'pranotaTagihanCatList', 'nomorPembayaran', 'totalPembayaran', 'akunCoa', 'akunBiaya', 'catPaymentCounter'));
    }

    /**
     * Store payment for pranota CAT
     */
    public function store(Request $request)
    {
        // Check permission manually to provide better error message
        if (! Gate::allows('pembayaran-pranota-cat-create')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota CAT. Silakan hubungi administrator.');
        }

        try {
            DB::beginTransaction();
            Log::info('Starting pembayaran pranota CAT store', $request->all());

            $penyesuaianInput = trim((string) $request->input('total_tagihan_penyesuaian', ''));
            if (preg_match('/^-?\d{1,3}(?:\.\d{3})+(?:,\d+)?$/', $penyesuaianInput)) {
                $penyesuaianInput = str_replace(',', '.', str_replace('.', '', $penyesuaianInput));
            } elseif (str_contains($penyesuaianInput, ',') && ! str_contains($penyesuaianInput, '.')) {
                $penyesuaianInput = str_replace(',', '.', $penyesuaianInput);
            }
            if ($request->has('total_tagihan_penyesuaian')) {
                $request->merge(['total_tagihan_penyesuaian' => $penyesuaianInput]);
            }

            $request->validate([
                'nomor_pembayaran' => 'required|string',
                'nomor_accurate' => 'nullable|string|max:255',
                'bank' => 'required|string|max:255',
                'akun_items' => 'required|array|min:1',
                'akun_items.*.akun_coa_id' => 'required|distinct|exists:akun_coa,id',
                'akun_items.*.nominal' => 'required|numeric|decimal:0,2|min:0.01',
                'akun_items.*.memo' => 'nullable|string|max:500',
                'jenis_transaksi' => 'required|in:debit,credit',
                'tanggal_kas' => 'required|date',
                'pranota_ids' => 'nullable|array',
                'pranota_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
                'pranota_tagihan_ids' => 'nullable|array',
                'pranota_tagihan_ids.*' => 'exists:pranota_tagihan_cat,id',
                'total_tagihan_penyesuaian' => 'nullable|numeric',
                'penyesuaian' => 'nullable|numeric',
                'alasan_penyesuaian' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            $pranotaIds = $request->input('pranota_ids', []);
            $pranotaTagihanIds = $request->input('pranota_tagihan_ids', []);

            if (empty($pranotaIds) && empty($pranotaTagihanIds)) {
                throw new \Exception('Silakan pilih minimal satu pranota CAT atau perbaikan untuk dibayar.');
            }

            $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', $request->input('penyesuaian', 0)));

            // Fetch and validate perbaikan pranotas
            $pranotas = \App\Models\PranotaPerbaikanKontainer::whereIn('id', $pranotaIds)->get();
            foreach ($pranotas as $pranota) {
                if ($pranota->status !== 'approved') {
                    throw new \Exception("Pranota {$pranota->nomor_pranota} statusnya bukan approved atau tidak dapat diproses");
                }
                $isPaid = PembayaranPranotaCatItem::where('pranota_perbaikan_kontainer_id', $pranota->id)->exists();
                if ($isPaid) {
                    throw new \Exception("Pranota {$pranota->nomor_pranota} sudah dibayar");
                }
            }

            // Fetch and validate tagihan pranotas
            $pranotaTagihans = \App\Models\PranotaTagihanCat::whereIn('id', $pranotaTagihanIds)->get();
            foreach ($pranotaTagihans as $pranota) {
                if ($pranota->status !== 'unpaid') {
                    throw new \Exception("Pranota Tagihan {$pranota->no_invoice} statusnya bukan unpaid atau tidak dapat diproses");
                }
                $isPaid = PembayaranPranotaCatItem::where('pranota_tagihan_cat_id', $pranota->id)->exists();
                if ($isPaid) {
                    throw new \Exception("Pranota Tagihan {$pranota->no_invoice} sudah dibayar");
                }
            }

            $totalPembayaran = $pranotas->sum(function ($pranota) {
                return $pranota->calculateTotalCatAmount();
            }) + $pranotaTagihans->sum(function ($pranota) {
                return $pranota->total_amount;
            });
            $totalSetelahPenyesuaian = $totalPembayaran + $penyesuaian;
            $akunItems = array_values($request->input('akun_items'));
            $this->validateAkunTotal($akunItems, $totalSetelahPenyesuaian);

            // Check for duplicate nomor_pembayaran
            $existingPayment = PembayaranPranotaCat::where('nomor_pembayaran', $request->nomor_pembayaran)->first();
            if ($existingPayment) {
                $request->merge(['nomor_pembayaran' => PembayaranPranotaCat::generateNomorPembayaran()]);
            }

            // Create pembayaran record
            $pembayaran = PembayaranPranotaCat::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_cetakan' => 1,
                'nomor_accurate' => $request->nomor_accurate,
                'bank' => $request->bank,
                'akun_coa_id' => $akunItems[0]['akun_coa_id'],
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalSetelahPenyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved',
            ]);
            $pembayaran->akunItems()->createMany($akunItems);

            // Create perbaikan payment items
            foreach ($pranotas as $pranota) {
                $paintAmount = $pranota->calculateTotalCatAmount();
                PembayaranPranotaCatItem::create([
                    'pembayaran_pranota_cat_id' => $pembayaran->id,
                    'pranota_perbaikan_kontainer_id' => $pranota->id,
                    'amount' => $paintAmount,
                ]);
            }

            // Create tagihan payment items and mark status paid
            foreach ($pranotaTagihans as $pranota) {
                $paintAmount = $pranota->total_amount;
                PembayaranPranotaCatItem::create([
                    'pembayaran_pranota_cat_id' => $pembayaran->id,
                    'pranota_tagihan_cat_id' => $pranota->id,
                    'amount' => $paintAmount,
                ]);
                $pranota->update(['status' => 'paid']);
            }

            $this->recordAccounting($pembayaran, $akunItems);

            DB::commit();

            $message = "Pembayaran pranota CAT berhasil dibuat dengan nomor: {$request->nomor_pembayaran}. ";
            $message .= 'Total item: '.(count($pranotaIds) + count($pranotaTagihanIds)).'. ';
            $message .= 'Status: Sudah dibayar.';

            return redirect()->route('pembayaran-pranota-cat.index')->with('success', $message);

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in pembayaran pranota CAT store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal membuat pembayaran: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        if (! Gate::allows('pembayaran-pranota-cat-update')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengedit pembayaran ini.');
        }

        $pembayaran = PembayaranPranotaCat::with(['pranotaTagihanCats', 'pranotaPerbaikanKontainers', 'akunCoa', 'akunItems.akunCoa'])->findOrFail($id);

        $akunCoa = Coa::where(function ($query) {
            $query->where('tipe_akun', 'Kas/Bank')
                ->orWhere('tipe_akun', 'Bank/Kas')
                ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                ->orWhere('tipe_akun', 'LIKE', '%Bank%');
        })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        $akunBiaya = Coa::orderBy('nomor_akun')->get(['id', 'nomor_akun', 'nama_akun']);

        return view('pembayaran-pranota-cat.edit', compact('pembayaran', 'akunCoa', 'akunBiaya'));
    }

    public function update(Request $request, $id)
    {
        if (! Gate::allows('pembayaran-pranota-cat-update')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengedit pembayaran ini.');
        }

        try {
            DB::beginTransaction();

            $pembayaran = PembayaranPranotaCat::with('akunItems')->findOrFail($id);

            $request->validate([
                'nomor_accurate' => 'nullable|string|max:255',
                'bank' => 'required|string|max:255',
                'akun_items' => 'required|array|min:1',
                'akun_items.*.akun_coa_id' => 'required|distinct|exists:akun_coa,id',
                'akun_items.*.nominal' => 'required|numeric|decimal:0,2|min:0.01',
                'akun_items.*.memo' => 'nullable|string|max:500',
                'jenis_transaksi' => 'required|in:debit,credit',
                'tanggal_kas' => 'required|date',
                'total_tagihan_penyesuaian' => 'nullable|numeric',
                'penyesuaian' => 'nullable|numeric',
                'alasan_penyesuaian' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', $request->input('penyesuaian', 0)));
            $totalPembayaran = $pembayaran->total_pembayaran;
            $totalSetelahPenyesuaian = $totalPembayaran + $penyesuaian;
            $akunItems = array_values($request->input('akun_items'));
            $this->validateAkunTotal($akunItems, $totalSetelahPenyesuaian);

            // Update COA Transaction (Delete old, create new)
            if (! $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran)) {
                throw new \RuntimeException('Jurnal lama gagal dihapus.');
            }

            // Update Pembayaran Record
            $pembayaran->update([
                'nomor_accurate' => $request->nomor_accurate,
                'bank' => $request->bank,
                'akun_coa_id' => $akunItems[0]['akun_coa_id'],
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalSetelahPenyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
            ]);
            $pembayaran->akunItems()->delete();
            $pembayaran->akunItems()->createMany($akunItems);
            $this->recordAccounting($pembayaran, $akunItems);

            DB::commit();

            return redirect()->route('pembayaran-pranota-cat.index')->with('success', 'Pembayaran pranota CAT berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in pembayaran pranota CAT update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pembayaran: '.$e->getMessage());
        }
    }

    private function validateAkunTotal(array $akunItems, float $total): void
    {
        $akunCents = collect($akunItems)->sum(fn ($item) => (int) round((float) $item['nominal'] * 100));
        if ($akunCents !== (int) round($total * 100)) {
            throw ValidationException::withMessages([
                'akun_items' => 'Jumlah nominal seluruh akun harus sama dengan total akhir pembayaran.',
            ]);
        }
    }

    private function recordAccounting(PembayaranPranotaCat $pembayaran, array $akunItems): void
    {
        $debitBank = $pembayaran->jenis_transaksi === 'debit';
        $total = (float) $pembayaran->total_setelah_penyesuaian;
        $tanggal = $pembayaran->tanggal_kas->toDateString();
        $referensi = $pembayaran->nomor_pembayaran;
        $jenis = 'Pembayaran Pranota CAT Kontainer';
        $keterangan = $jenis.' - '.$referensi;
        if ($pembayaran->keterangan) {
            $keterangan .= ' | '.$pembayaran->keterangan;
        }
        if ($pembayaran->alasan_penyesuaian) {
            $keterangan .= ' | Penyesuaian: '.$pembayaran->alasan_penyesuaian;
        }

        if (! $this->coaTransactionService->recordTransaction(
            $pembayaran->bank, $debitBank ? $total : 0, $debitBank ? 0 : $total,
            $tanggal, $referensi, $jenis, $keterangan
        )) {
            throw new \RuntimeException('Jurnal akun Bank/Kas gagal dicatat.');
        }

        foreach ($akunItems as $item) {
            $akun = Coa::findOrFail($item['akun_coa_id']);
            $nominal = (float) $item['nominal'];
            $memo = trim((string) ($item['memo'] ?? ''));
            if (! $this->coaTransactionService->recordTransaction(
                $akun->nama_akun, $debitBank ? 0 : $nominal, $debitBank ? $nominal : 0,
                $tanggal, $referensi, $jenis, $memo === '' ? $keterangan : $keterangan.' | '.$memo, $akun->id
            )) {
                throw new \RuntimeException('Jurnal akun biaya gagal dicatat.');
            }
        }
    }

    public function print($id)
    {
        $pembayaran = PembayaranPranotaCat::with(['pranotaTagihanCats', 'pranotaPerbaikanKontainers', 'akunCoa', 'akunItems.akunCoa'])->findOrFail($id);

        return view('pembayaran-pranota-cat.print', compact('pembayaran'));
    }
}
