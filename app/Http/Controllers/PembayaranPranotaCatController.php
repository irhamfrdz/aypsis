<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranota;
use App\Models\PembayaranPranotaItem;
use App\Models\Pranota;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembayaranPranotaCatController extends Controller
{
    public function index()
    {
        // Get all pembayaran_pranota that have pranota items related to CAT
        $pembayaranList = PembayaranPranota::whereHas('pranotas', function($query) {
            $query->whereHas('tagihanCat');
        })
        ->with(['pranotas.tagihanCat'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('pembayaran-pranota-cat.index', compact('pembayaranList'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranPranota::with(['pranotas.tagihanCat'])->findOrFail($id);

        return view('pembayaran-pranota-cat.show', compact('pembayaran'));
    }

    /**
     * Show form to select pranota CAT for payment
     */
    public function create(Request $request)
    {
        // If pranota_ids are provided (from pranota index page), redirect to payment form
        if ($request->has('pranota_ids') && !empty($request->pranota_ids)) {
            return $this->showPaymentForm($request);
        }

        // Clear any old validation errors from session for fresh form load
        if ($request->isMethod('get')) {
            session()->forget('errors');
        }

        // Get all pranota CAT that are unpaid (not paid yet)
        $pranotaList = Pranota::where('status', 'unpaid')
            ->whereHas('tagihanCat')
            ->with('tagihanCat')
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
            'pranota_ids.*' => 'exists:pranotalist,id'
        ]);

        $pranotaIds = $request->input('pranota_ids');
        $pranotaList = Pranota::whereIn('id', $pranotaIds)->with('tagihanCat')->get();

        // Validate that all selected pranota are unpaid and have CAT tagihan
        foreach ($pranotaList as $pranota) {
            if ($pranota->status !== 'unpaid') {
                return redirect()->back()->with('error', "Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
            }
            if (!$pranota->tagihanCat()->exists()) {
                return redirect()->back()->with('error', "Pranota {$pranota->no_invoice} bukan pranota CAT");
            }
        }

        // Generate nomor pembayaran (akan diupdate berdasarkan bank yang dipilih)
        $nomorPembayaran = '';
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
        $request->validate([
            'nomor_pembayaran' => 'required|string|unique:pembayaran_pranota',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:debit,credit',
            'tanggal_kas' => 'required|date',
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranotalist,id',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $pranotaIds = $request->input('pranota_ids');
            $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', 0));

            // Get and validate pranota records
            $pranotas = Pranota::whereIn('id', $pranotaIds)->with('tagihanCat')->get();

            foreach ($pranotas as $pranota) {
                if ($pranota->status !== 'unpaid') {
                    throw new \Exception("Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
                }
                if (!$pranota->tagihanCat()->exists()) {
                    throw new \Exception("Pranota {$pranota->no_invoice} bukan pranota CAT");
                }
            }

            $totalPembayaran = $pranotas->sum('total_amount');

            // Create pembayaran record
            $pembayaran = PembayaranPranota::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
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

            // Create payment items and update pranota status
            foreach ($pranotas as $pranota) {
                PembayaranPranotaItem::create([
                    'pembayaran_pranota_id' => $pembayaran->id,
                    'pranota_id' => $pranota->id,
                    'amount' => $pranota->total_amount
                ]);

                // Update pranota status to paid
                $pranota->update(['status' => 'paid']);
            }

            DB::commit();

            $message = "Pembayaran pranota CAT berhasil dibuat dengan nomor: {$request->nomor_pembayaran}. ";
            $message .= "Total pranota: " . count($pranotaIds) . ". ";
            $message .= "Status: Sudah dibayar.";

            return redirect()->route('pembayaran-pranota-cat.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }
}
