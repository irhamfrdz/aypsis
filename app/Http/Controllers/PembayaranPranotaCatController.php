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

class PembayaranPranotaCatController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }

    public function index()
    {
        // Get all pembayaran_pranota_cat
        $pembayaranList = PembayaranPranotaCat::with(['pranotaTagihanCats', 'pranotaPerbaikanKontainers'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembayaran-pranota-cat.index', compact('pembayaranList'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranPranotaCat::with(['pranotaTagihanCats', 'pranotaPerbaikanKontainers'])->findOrFail($id);

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

        return view('pembayaran-pranota-cat.create', compact('pranotaList', 'pranotaTagihanCatList', 'akunCoa'));
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
            $isPaid = \App\Models\PembayaranPranotaCatItem::where('pranota_perbaikan_kontainer_id', $pranota->id)->exists();
            if ($isPaid) {
                return redirect()->back()->with('error', "Pranota {$pranota->nomor_pranota} sudah dibayar");
            }
        }

        // Validate that all selected tagihan are unpaid and not paid yet
        foreach ($pranotaTagihanCatList as $pranota) {
            if ($pranota->status !== 'unpaid') {
                return redirect()->back()->with('error', "Pranota Tagihan {$pranota->no_invoice} statusnya bukan unpaid atau tidak dapat diproses");
            }
            $isPaid = \App\Models\PembayaranPranotaCatItem::where('pranota_tagihan_cat_id', $pranota->id)->exists();
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

        return view('pembayaran-pranota-cat.payment-form', compact('pranotaList', 'pranotaTagihanCatList', 'nomorPembayaran', 'totalPembayaran', 'akunCoa'));
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

            $request->validate([
                'nomor_pembayaran' => 'required|string',
                'nomor_accurate' => 'nullable|string|max:255',
                'bank' => 'required|string|max:255',
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
                $isPaid = \App\Models\PembayaranPranotaCatItem::where('pranota_perbaikan_kontainer_id', $pranota->id)->exists();
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
                $isPaid = \App\Models\PembayaranPranotaCatItem::where('pranota_tagihan_cat_id', $pranota->id)->exists();
                if ($isPaid) {
                    throw new \Exception("Pranota Tagihan {$pranota->no_invoice} sudah dibayar");
                }
            }

            $totalPembayaran = $pranotas->sum(function ($pranota) {
                return $pranota->calculateTotalCatAmount();
            }) + $pranotaTagihans->sum(function ($pranota) {
                return $pranota->total_amount;
            });

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
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved',
            ]);

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

            // Record Coa Double Entry Transaction
            $totalSetelahPenyesuaian = $totalPembayaran + $penyesuaian;
            $tanggalTransaksi = $request->tanggal_kas;

            $keterangan = 'Pembayaran Pranota CAT Kontainer - '.$request->nomor_pembayaran;
            if ($request->keterangan) {
                $keterangan .= ' | '.$request->keterangan;
            }
            if ($request->alasan_penyesuaian) {
                $keterangan .= ' | Penyesuaian: '.$request->alasan_penyesuaian;
            }

            $this->coaTransactionService->recordDoubleEntry(
                ['nama_akun' => 'Biaya CAT Kontainer', 'jumlah' => $totalSetelahPenyesuaian],
                ['nama_akun' => $request->bank, 'jumlah' => $totalSetelahPenyesuaian],
                $tanggalTransaksi,
                $request->nomor_pembayaran,
                'Pembayaran Pranota CAT Kontainer',
                $keterangan
            );

            DB::commit();

            $message = "Pembayaran pranota CAT berhasil dibuat dengan nomor: {$request->nomor_pembayaran}. ";
            $message .= 'Total item: '.(count($pranotaIds) + count($pranotaTagihanIds)).'. ';
            $message .= 'Status: Sudah dibayar.';

            return redirect()->route('pembayaran-pranota-cat.index')->with('success', $message);

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

    public function print($id)
    {
        $pembayaran = PembayaranPranotaCat::with(['pranotaTagihanCats', 'pranotaPerbaikanKontainers'])->findOrFail($id);

        return view('pembayaran-pranota-cat.print', compact('pembayaran'));
    }
}
