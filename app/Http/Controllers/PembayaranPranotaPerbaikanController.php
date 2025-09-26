<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranota;
use App\Models\PembayaranPranotaItem;
use App\Models\Pranota;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembayaranPranotaPerbaikanController extends Controller
{
    public function index()
    {
        // Get all pembayaran_pranota that have pranota items related to perbaikan
        // Filter pranota that have perbaikan kontainer in their tagihan_ids
        $pembayaranList = PembayaranPranota::whereHas('pranotas', function($query) {
            $query->whereNotNull('tagihan_ids')
                  ->where('tagihan_ids', '!=', '[]');
        })
        ->with(['pranotas'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('pembayaran-pranota-perbaikan.index', compact('pembayaranList'));
    }

    public function show($id)
    {
        $pembayaran = PembayaranPranota::with(['pranotas.perbaikanKontainer'])->findOrFail($id);

        return view('pembayaran-pranota-perbaikan.show', compact('pembayaran'));
    }

    /**
     * Show form to select pranota perbaikan for payment
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

        // Get all pranota that are unpaid and have perbaikan items
        $pranotaList = Pranota::where('status', 'unpaid')
            ->whereHas('perbaikanKontainer')
            ->with('perbaikanKontainer')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get akun_coa data for bank selection
        $akunCoa = Coa::orderBy('nama_akun')->get();

        return view('pembayaran-pranota-perbaikan.create', compact('pranotaList', 'akunCoa'));
    }

    /**
     * Show payment form for selected pranota perbaikan
     */
    public function showPaymentForm(Request $request)
    {
        $request->validate([
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranotalist,id'
        ]);

        $pranotaIds = $request->input('pranota_ids');
        $pranotaList = Pranota::whereIn('id', $pranotaIds)->with('perbaikanKontainer')->get();

        // Validate that all selected pranota are unpaid and have perbaikan tagihan
        foreach ($pranotaList as $pranota) {
            if ($pranota->status !== 'unpaid') {
                return redirect()->back()->with('error', "Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
            }
            if (!$pranota->perbaikanKontainer()->exists()) {
                return redirect()->back()->with('error', "Pranota {$pranota->no_invoice} bukan pranota perbaikan");
            }
        }

        // Generate nomor pembayaran (akan diupdate berdasarkan bank yang dipilih)
        $nomorPembayaran = '';
        $totalPembayaran = $pranotaList->sum('total_amount');

        // Get akun_coa data for bank selection
        $akunCoa = Coa::orderBy('nama_akun')->get();

        return view('pembayaran-pranota-perbaikan.payment-form', compact('pranotaList', 'nomorPembayaran', 'totalPembayaran', 'akunCoa'));
    }

    /**
     * Store payment for pranota perbaikan
     */
    public function store(Request $request)
    {
        $request->validate([
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranotalist,id',
            'bank' => 'required|string',
            'jenis_transaksi' => 'required|string',
            'tanggal_kas' => 'required|date',
            'total_pembayaran' => 'required|numeric|min:0',
            'penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor pembayaran based on bank
            $bankCode = substr($request->bank, 0, 3);
            $nomorPembayaran = 'PP-' . $bankCode . '-' . date('ymd') . '-' . str_pad(PembayaranPranota::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create pembayaran record
            $pembayaran = PembayaranPranota::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $request->total_pembayaran,
                'penyesuaian' => $request->penyesuaian ?? 0,
                'total_setelah_penyesuaian' => $request->total_pembayaran + ($request->penyesuaian ?? 0),
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'completed'
            ]);

            // Attach pranota to pembayaran with amounts
            foreach ($request->pranota_ids as $pranotaId) {
                $pranota = Pranota::find($pranotaId);
                $pembayaran->pranotas()->attach($pranotaId, [
                    'amount' => $pranota->total_amount
                ]);

                // Update pranota status to paid
                $pranota->update(['status' => 'paid']);
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-perbaikan.index')
                ->with('success', 'Pembayaran pranota perbaikan berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
