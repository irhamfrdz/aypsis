<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaCat;
use App\Models\PembayaranPranotaCatItem;
use App\Models\PranotaTagihanCat;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PembayaranPranotaCatController extends Controller
{
    public function index()
    {
        // Get all pembayaran_pranota_cat
        $pembayaranList = PembayaranPranotaCat::with(['pranotaTagihanCats'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembayaran-pranota-cat.index', compact('pembayaranList'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranPranotaCat::with(['pranotaTagihanCats'])->findOrFail($id);

        return view('pembayaran-pranota-cat.show', compact('pembayaran'));
    }

    /**
     * Show form to select pranota CAT for payment
     */
    public function create(Request $request)
    {
        // Check permission manually to provide better error message
        if (!Gate::allows('pembayaran-pranota-cat-create')) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota CAT. Silakan hubungi administrator.');
        }

        // If pranota_ids are provided (from pranota index page), redirect to payment form
        if ($request->has('pranota_ids') && !empty($request->pranota_ids)) {
            return $this->showPaymentForm($request);
        }

        // Clear any old validation errors from session for fresh form load
        if ($request->isMethod('get')) {
            session()->forget('errors');
        }

        // Get all pranota CAT that are unpaid (not paid yet)
        $pranotaList = PranotaTagihanCat::where('status', 'unpaid')
            ->whereNotNull('tagihan_cat_ids')
            ->where('tagihan_cat_ids', '!=', '[]')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get akun_coa data for bank selection
        $akunCoa = Coa::orderBy('nama_akun')->get();

        return view('pembayaran-pranota-cat.create', compact('pranotaList', 'akunCoa'));
    }

    /**
     * Show payment form for selected pranota CAT
     */
    public function showPaymentForm(Request $request)
    {
        $request->validate([
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranota_tagihan_cat,id'
        ]);

        $pranotaIds = $request->input('pranota_ids');
        $pranotaList = PranotaTagihanCat::whereIn('id', $pranotaIds)->get();

        // Validate that all selected pranota are unpaid and have CAT tagihan
        foreach ($pranotaList as $pranota) {
            if ($pranota->status !== 'unpaid') {
                return redirect()->back()->with('error', "Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
            }
            if (empty($pranota->tagihan_cat_ids)) {
                return redirect()->back()->with('error', "Pranota {$pranota->no_invoice} bukan pranota CAT");
            }
        }

        // Generate nomor pembayaran (akan diupdate berdasarkan bank yang dipilih)
        $nomorPembayaran = $request->input('nomor_pembayaran', '');
        if (empty($nomorPembayaran)) {
            $nomorPembayaran = PembayaranPranotaCat::generateNomorPembayaran();
        }
        $totalPembayaran = $pranotaList->sum('total_amount');

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
        if (!Gate::allows('pembayaran-pranota-cat-create')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak memiliki izin untuk membuat pembayaran pranota CAT. Silakan hubungi administrator.');
        }

        try {
            DB::beginTransaction();
            Log::info('Starting pembayaran pranota CAT store', $request->all());

            $request->validate([
                'nomor_pembayaran' => 'required|string',
                'bank' => 'required|string|max:255',
                'jenis_transaksi' => 'required|in:debit,credit',
                'tanggal_kas' => 'required|date',
                'pranota_ids' => 'required|array|min:1',
                'pranota_ids.*' => 'exists:pranota_tagihan_cat,id',
                'total_tagihan_penyesuaian' => 'nullable|numeric',
                'alasan_penyesuaian' => 'nullable|string',
                'keterangan' => 'nullable|string'
            ]);

            $pranotaIds = $request->input('pranota_ids');
            $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', 0));

            // Get and validate pranota records
            $pranotas = PranotaTagihanCat::whereIn('id', $pranotaIds)->get();
            Log::info('Found pranotas', ['count' => $pranotas->count(), 'ids' => $pranotaIds]);

            foreach ($pranotas as $pranota) {
                if ($pranota->status !== 'unpaid') {
                    throw new \Exception("Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
                }
                if (empty($pranota->tagihan_cat_ids)) {
                    throw new \Exception("Pranota {$pranota->no_invoice} bukan pranota CAT");
                }
            }

            $totalPembayaran = $pranotas->sum('total_amount');
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
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved'
            ]);
            Log::info('Pembayaran record created', ['id' => $pembayaran->id]);

            // Create payment items and update pranota status
            foreach ($pranotas as $pranota) {
                PembayaranPranotaCatItem::create([
                    'pembayaran_pranota_cat_id' => $pembayaran->id,
                    'pranota_tagihan_cat_id' => $pranota->id,
                    'amount' => $pranota->total_amount
                ]);
                Log::info('Payment item created', ['pranota_id' => $pranota->id]);

                // Update pranota status to paid
                $pranota->update(['status' => 'paid']);
                Log::info('Pranota status updated', ['pranota_id' => $pranota->id]);
            }

            DB::commit();
            Log::info('Transaction committed successfully');

            $message = "Pembayaran pranota CAT berhasil dibuat dengan nomor: {$request->nomor_pembayaran}. ";
            $message .= "Total pranota: " . count($pranotaIds) . ". ";
            $message .= "Status: Sudah dibayar.";

            return redirect()->route('pembayaran-pranota-cat.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in pembayaran pranota CAT store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $pembayaran = PembayaranPranotaCat::with(['pranotaTagihanCats'])->findOrFail($id);

        return view('pembayaran-pranota-cat.print', compact('pembayaran'));
    }
}
