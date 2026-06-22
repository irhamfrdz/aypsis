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

        // If pranota_ids are provided (from pranota index page), redirect to payment form
        if ($request->has('pranota_ids') && ! empty($request->pranota_ids)) {
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

        // Get Bank/Kas accounts only, sorted by account number
        $akunCoa = Coa::where(function ($query) {
            $query->where('tipe_akun', 'Kas/Bank')
                ->orWhere('tipe_akun', 'Bank/Kas')
                ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                ->orWhere('tipe_akun', 'LIKE', '%Bank%');
        })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        return view('pembayaran-pranota-cat.create', compact('pranotaList', 'akunCoa'));
    }

    /**
     * Show payment form for selected pranota CAT
     */
    public function showPaymentForm(Request $request)
    {
        $request->validate([
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
        ]);

        $pranotaIds = $request->input('pranota_ids');
        $pranotaList = \App\Models\PranotaPerbaikanKontainer::whereIn('id', $pranotaIds)->get();

        // Validate that all selected pranota are approved and have not been paid yet
        foreach ($pranotaList as $pranota) {
            if ($pranota->status !== 'approved') {
                return redirect()->back()->with('error', "Pranota {$pranota->nomor_pranota} statusnya bukan approved atau tidak dapat diproses");
            }
            $isPaid = \App\Models\PembayaranPranotaCatItem::where('pranota_perbaikan_kontainer_id', $pranota->id)->exists();
            if ($isPaid) {
                return redirect()->back()->with('error', "Pranota {$pranota->nomor_pranota} sudah dibayar");
            }
        }

        // Generate nomor pembayaran (akan diupdate berdasarkan bank yang dipilih)
        $nomorPembayaran = $request->input('nomor_pembayaran', '');
        if (empty($nomorPembayaran)) {
            $nomorPembayaran = PembayaranPranotaCat::generateNomorPembayaran();
        }
        $totalPembayaran = $pranotaList->sum(function ($pranota) {
            return $pranota->calculateTotalCatAmount();
        });

        // Get akun_coa data for bank selection
        $akunCoa = Coa::orderBy('nama_akun')->get();

        return view('pembayaran-pranota-cat.payment-form', compact('pranotaList', 'nomorPembayaran', 'totalPembayaran', 'akunCoa'));
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
                'pranota_ids' => 'required|array|min:1',
                'pranota_ids.*' => 'exists:pranota_perbaikan_kontainers,id',
                'total_tagihan_penyesuaian' => 'nullable|numeric',
                'penyesuaian' => 'nullable|numeric',
                'alasan_penyesuaian' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            $pranotaIds = $request->input('pranota_ids');
            $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', $request->input('penyesuaian', 0)));

            // Get and validate pranota records
            $pranotas = \App\Models\PranotaPerbaikanKontainer::whereIn('id', $pranotaIds)->get();
            Log::info('Found pranotas', ['count' => $pranotas->count(), 'ids' => $pranotaIds]);

            foreach ($pranotas as $pranota) {
                if ($pranota->status !== 'approved') {
                    throw new \Exception("Pranota {$pranota->nomor_pranota} statusnya bukan approved atau tidak dapat diproses");
                }
                $isPaid = \App\Models\PembayaranPranotaCatItem::where('pranota_perbaikan_kontainer_id', $pranota->id)->exists();
                if ($isPaid) {
                    throw new \Exception("Pranota {$pranota->nomor_pranota} sudah dibayar");
                }
            }

            $totalPembayaran = $pranotas->sum(function ($pranota) {
                return $pranota->calculateTotalCatAmount();
            });
            Log::info('Calculated total pembayaran', ['total' => $totalPembayaran]);

            // Check for duplicate nomor_pembayaran
            $existingPayment = PembayaranPranotaCat::where('nomor_pembayaran', $request->nomor_pembayaran)->first();
            if ($existingPayment) {
                // If duplicate found, generate a new number
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
            Log::info('Pembayaran record created', ['id' => $pembayaran->id]);

            // Create payment items
            foreach ($pranotas as $pranota) {
                $paintAmount = $pranota->calculateTotalCatAmount();
                PembayaranPranotaCatItem::create([
                    'pembayaran_pranota_cat_id' => $pembayaran->id,
                    'pranota_perbaikan_kontainer_id' => $pranota->id,
                    'amount' => $paintAmount,
                ]);
                Log::info('Payment item created', ['pranota_id' => $pranota->id]);
            }

            // Catat transaksi menggunakan double-entry COA
            $totalSetelahPenyesuaian = $totalPembayaran + $penyesuaian;
            $tanggalTransaksi = $request->tanggal_kas;

            $keterangan = 'Pembayaran Pranota CAT Kontainer - '.$request->nomor_pembayaran;
            if ($request->keterangan) {
                $keterangan .= ' | '.$request->keterangan;
            }
            if ($request->alasan_penyesuaian) {
                $keterangan .= ' | Penyesuaian: '.$request->alasan_penyesuaian;
            }

            // Catat transaksi double-entry: Biaya CAT Kontainer (Debit) dan Bank (Kredit)
            $this->coaTransactionService->recordDoubleEntry(
                ['nama_akun' => 'Biaya CAT Kontainer', 'jumlah' => $totalSetelahPenyesuaian],
                ['nama_akun' => $request->bank, 'jumlah' => $totalSetelahPenyesuaian],
                $tanggalTransaksi,
                $request->nomor_pembayaran,
                'Pembayaran Pranota CAT Kontainer',
                $keterangan
            );

            DB::commit();
            Log::info('Transaction committed successfully');

            $message = "Pembayaran pranota CAT berhasil dibuat dengan nomor: {$request->nomor_pembayaran}. ";
            $message .= 'Total pranota: '.count($pranotaIds).'. ';
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
