<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaKontainer;
use App\Models\PembayaranPranotaKontainerItem;
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembayaranPranotaKontainerController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index()
    {
        $pembayaranList = PembayaranPranotaKontainer::with(['pembuatPembayaran', 'penyetujuPembayaran', 'items.pranota'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembayaran-pranota-kontainer.index', compact('pembayaranList'));
    }

    /**
     * Show form to select pranota for payment
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

        // Get all pranota that are unpaid (not paid yet)
        $pranotaList = PranotaTagihanKontainerSewa::where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get akun_coa data for bank selection
        $akunCoa = Coa::orderBy('nama_akun')->get();

        return view('pembayaran-pranota-kontainer.create', compact('pranotaList', 'akunCoa'));
    }

    /**
     * Show payment form for selected pranota
     */
    public function showPaymentForm(Request $request)
    {
        $request->validate([
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranota_tagihan_kontainer_sewa,id'
        ]);

        $pranotaIds = $request->input('pranota_ids');
        $pranotaList = PranotaTagihanKontainerSewa::whereIn('id', $pranotaIds)->get();

        // Validate that all selected pranota are unpaid
        foreach ($pranotaList as $pranota) {
            if ($pranota->status === 'paid' || $pranota->status === 'cancelled') {
                return redirect()->back()->with('error', "Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
            }
        }

        // Generate nomor pembayaran
        $nomorPembayaran = PembayaranPranotaKontainer::generateNomorPembayaran();
        $totalPembayaran = $pranotaList->sum('total_amount');

        // Get akun_coa data for bank selection
        $akunCoa = Coa::orderBy('nama_akun')->get();

        return view('pembayaran-pranota-kontainer.payment-form', compact('pranotaList', 'nomorPembayaran', 'totalPembayaran', 'akunCoa'));
    }

    /**
     * Store payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_pembayaran' => 'required|string|unique:pembayaran_pranota_kontainer',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'tanggal_kas' => 'required|date_format:d/m/Y',
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranota_tagihan_kontainer_sewa,id',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $pranotaIds = $request->input('pranota_ids');
            $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', 0));

            // Get and validate pranota records
            $pranotas = PranotaTagihanKontainerSewa::whereIn('id', $pranotaIds)->get();

            foreach ($pranotas as $pranota) {
                if ($pranota->status === 'paid' || $pranota->status === 'cancelled') {
                    throw new \Exception("Pranota {$pranota->no_invoice} sudah dibayar atau tidak dapat diproses");
                }
            }

            $totalPembayaran = $pranotas->sum('total_amount');

            // Create pembayaran record
            $pembayaran = PembayaranPranotaKontainer::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_kas)->format('Y-m-d'),
                'tanggal_pembayaran' => now()->toDateString(),
                'total_pembayaran' => $totalPembayaran,
                'total_tagihan_penyesuaian' => $penyesuaian,
                'total_tagihan_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved',
                'dibuat_oleh' => Auth::id(),
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now()
            ]);

            // Create payment items and update pranota status
            foreach ($pranotas as $pranota) {
                PembayaranPranotaKontainerItem::create([
                    'pembayaran_pranota_kontainer_id' => $pembayaran->id,
                    'pranota_id' => $pranota->id,
                    'amount' => $pranota->total_amount
                ]);

                // Update pranota status to paid
                $pranota->update(['status' => 'paid']);
            }

            DB::commit();

            $message = "Pembayaran berhasil dibuat dengan nomor: {$request->nomor_pembayaran}. ";
            $message .= "Total pranota: " . count($pranotaIds) . ". ";
            $message .= "Status: Sudah dibayar.";

            return redirect()->route('pembayaran-pranota-kontainer.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $pembayaran = PembayaranPranotaKontainer::with([
            'items.pranota',
            'pembuatPembayaran',
            'penyetujuPembayaran'
        ])->findOrFail($id);

        return view('pembayaran-pranota-kontainer.show', compact('pembayaran'));
    }

    /**
     * Print payment receipt
     */
    public function print($id)
    {
        $pembayaran = PembayaranPranotaKontainer::with([
            'items.pranota',
            'pembuatPembayaran',
            'penyetujuPembayaran'
        ])->findOrFail($id);

        return view('pembayaran-pranota-kontainer.print', compact('pembayaran'));
    }

    /**
     * Show edit form for payment
     */
    public function edit($id)
    {
        $pembayaran = PembayaranPranotaKontainer::with([
            'items.pranota',
            'pembuatPembayaran',
            'penyetujuPembayaran'
        ])->findOrFail($id);

        return view('pembayaran-pranota-kontainer.edit', compact('pembayaran'));
    }

    /**
     * Update payment
     */
    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranPranotaKontainer::findOrFail($id);

        $request->validate([
            'nomor_pembayaran' => 'required|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'tanggal_kas' => 'required|date',
            'bank' => 'required|string|max:255',
            'jenis_transaksi' => 'required|in:Debit,Kredit',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'keterangan' => 'nullable|string'
        ]);

        $pembayaran->update([
            'nomor_pembayaran' => $request->nomor_pembayaran,
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'tanggal_kas' => $request->tanggal_kas,
            'bank' => $request->bank,
            'jenis_transaksi' => $request->jenis_transaksi,
            'total_pembayaran' => $request->total_pembayaran,
            'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian ?? 0,
            'total_tagihan_setelah_penyesuaian' => $request->total_pembayaran + ($request->total_tagihan_penyesuaian ?? 0),
            'keterangan' => $request->keterangan,
            'diupdate_oleh' => Auth::id()
        ]);

        return redirect()->route('pembayaran-pranota-kontainer.index')
            ->with('success', 'Pembayaran berhasil diupdate');
    }

    /**
     * Delete payment
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pembayaran = PembayaranPranotaKontainer::findOrFail($id);

            // Update status pranota back to unpaid
            foreach ($pembayaran->items as $item) {
                $pranota = $item->pranota;
                if ($pranota) {
                    $pranota->update(['status' => 'unpaid']);
                }
            }

            // Delete payment items first
            $pembayaran->items()->delete();

            // Delete payment
            $pembayaran->delete();

            DB::commit();

            return redirect()->route('pembayaran-pranota-kontainer.index')
                ->with('success', 'Pembayaran berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Remove pranota from payment
     */
    public function removePranota($pembayaranId, $pranotaId)
    {
        try {
            DB::beginTransaction();

            $pembayaran = PembayaranPranotaKontainer::findOrFail($pembayaranId);
            $item = $pembayaran->items()->where('pranota_id', $pranotaId)->first();

            if (!$item) {
                return response()->json(['error' => 'Pranota tidak ditemukan dalam pembayaran ini'], 404);
            }

            // Update pranota status back to unpaid
            $pranota = $item->pranota;
            if ($pranota) {
                $pranota->update(['status' => 'unpaid']);
            }

            // Remove the item
            $item->delete();

            // Recalculate total pembayaran
            $newTotal = $pembayaran->items()->sum('amount');
            $pembayaran->update([
                'total_pembayaran' => $newTotal,
                'total_tagihan_setelah_penyesuaian' => $newTotal + ($pembayaran->total_tagihan_penyesuaian ?? 0)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pranota berhasil dihapus dari pembayaran',
                'new_total' => number_format($newTotal, 0, ',', '.'),
                'new_final_total' => number_format($newTotal + ($pembayaran->total_tagihan_penyesuaian ?? 0), 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal menghapus pranota: ' . $e->getMessage()], 500);
        }
    }
}
