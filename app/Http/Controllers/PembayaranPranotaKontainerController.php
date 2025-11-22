<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaKontainer;
use App\Models\PembayaranPranotaKontainerItem;
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\Coa;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PembayaranPranotaKontainerController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }
    /**
     * Display a listing of payments
     */
    public function index()
    {
        $pembayaranList = PembayaranPranotaKontainer::with(['pembuatPembayaran', 'penyetujuPembayaran', 'items.pranota', 'dpPayment'])
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

        // Get akun_coa data for bank selection - only kas/bank type
        $akunCoa = Coa::where(function($query) {
                $query->where('tipe_akun', 'Kas/Bank')
                      ->orWhere('tipe_akun', 'Bank/Kas')
                      ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                      ->orWhere('tipe_akun', 'LIKE', '%Bank%');
            })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

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

        // Generate nomor pembayaran (preview only, tidak update database)
        $nomorPembayaran = PembayaranPranotaKontainer::generateNomorPembayaran();
        $totalPembayaran = $pranotaList->sum('total_amount');

        // Get akun_coa data for bank selection - only kas/bank type
        $akunCoa = Coa::where(function($query) {
                $query->where('tipe_akun', 'Kas/Bank')
                      ->orWhere('tipe_akun', 'Bank/Kas')
                      ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                      ->orWhere('tipe_akun', 'LIKE', '%Bank%');
            })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

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
            'keterangan' => 'nullable|string',
            'selected_dp_id' => 'nullable|exists:pembayaran_aktivitas_lainnya,id',
            'selected_dp_amount' => 'nullable|numeric'
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
            $dpAmount = floatval($request->input('selected_dp_amount', 0));

            // Debug logging untuk troubleshooting
            Log::info('DP Calculation Debug', [
                'total_pembayaran' => $totalPembayaran,
                'penyesuaian' => $penyesuaian,
                'dp_amount' => $dpAmount,
                'selected_dp_id' => $request->input('selected_dp_id'),
                'calculation_result' => ($totalPembayaran + $penyesuaian) - $dpAmount
            ]);

            // Create pembayaran record
            $pembayaran = PembayaranPranotaKontainer::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'tanggal_kas' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_kas)->format('Y-m-d'),
                'tanggal_pembayaran' => now()->toDateString(),
                'total_pembayaran' => $totalPembayaran,
                'total_tagihan_penyesuaian' => $penyesuaian,
                'total_tagihan_setelah_penyesuaian' => ($totalPembayaran + $penyesuaian) - $dpAmount,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved',
                'dibuat_oleh' => Auth::id(),
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
                'dp_payment_id' => $request->selected_dp_id,
                'dp_amount' => $dpAmount
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

                // Update status_pranota to 'paid' for all related tagihan kontainer sewa items
                $tagihanIds = $pranota->tagihan_kontainer_sewa_ids;
                if (is_array($tagihanIds) && !empty($tagihanIds)) {
                    \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $tagihanIds)
                        ->update(['status_pranota' => 'paid']);
                }
            }

            // Catat transaksi ke akun bank yang dipilih
            $totalAkhir = ($totalPembayaran + $penyesuaian) - $dpAmount;
            $tanggalTransaksi = \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_kas)->format('Y-m-d');

            $keterangan = "Pembayaran Pranota Kontainer - " . $request->nomor_pembayaran;
            if ($request->keterangan) {
                $keterangan .= " | " . $request->keterangan;
            }

            // Catat transaksi ke akun bank (kredit - mengurangi saldo bank)
            $this->coaTransactionService->recordTransaction(
                $request->bank,              // nama_akun
                0,                          // debit (tidak ada)
                $totalAkhir,                // kredit (mengurangi saldo bank)
                $tanggalTransaksi,          // tanggal_transaksi
                $request->nomor_pembayaran, // nomor_referensi
                'Pembayaran Pranota Kontainer', // jenis_transaksi
                $keterangan                 // keterangan
            );

            // Update nomor terakhir after successful payment creation
            // Extract the running number from the nomor_pembayaran (last 6 digits)
            $nomorPembayaran = $request->nomor_pembayaran;
            $runningNumber = (int) substr($nomorPembayaran, -6);

            // Update master nomor terakhir
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')->lockForUpdate()->first();
            if ($nomorTerakhir) {
                $nomorTerakhir->nomor_terakhir = $runningNumber;
                $nomorTerakhir->save();
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
            'penyetujuPembayaran',
            'dpPayment'
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
            'penyetujuPembayaran',
            'dpPayment'
        ])->findOrFail($id);

        // Get COA transactions related to this payment
        $coaTransactions = \App\Models\CoaTransaction::with('coa')
            ->where('nomor_referensi', $pembayaran->nomor_pembayaran)
            ->orderBy('id', 'asc')
            ->get();

        return view('pembayaran-pranota-kontainer.print', compact('pembayaran', 'coaTransactions'));
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
        try {
            DB::beginTransaction();

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

            // Calculate old and new total
            $oldTotal = $pembayaran->total_tagihan_setelah_penyesuaian;
            $newTotal = $request->total_pembayaran + ($request->total_tagihan_penyesuaian ?? 0);
            $difference = $newTotal - $oldTotal;

            // Update saldo bank jika ada perubahan total atau bank berubah
            if ($difference != 0 || $pembayaran->bank != $request->bank) {
                // Reverse old bank transaction
                $oldBankCoa = Coa::where('nama_akun', $pembayaran->bank)->first();
                if ($oldBankCoa) {
                    if ($pembayaran->jenis_transaksi === 'Debit') {
                        $oldBankCoa->saldo += $oldTotal;
                    } else {
                        $oldBankCoa->saldo -= $oldTotal;
                    }
                    $oldBankCoa->save();
                }

                // Apply new bank transaction
                $newBankCoa = Coa::where('nama_akun', $request->bank)->first();
                if ($newBankCoa) {
                    if ($request->jenis_transaksi === 'Debit') {
                        $newBankCoa->saldo -= $newTotal;
                    } else {
                        $newBankCoa->saldo += $newTotal;
                    }
                    $newBankCoa->save();
                }
            }

            // Update saldo Biaya Sewa Kontainer jika ada perubahan total
            if ($difference != 0) {
                $biayaSewaKontainerCoa = Coa::where('nama_akun', 'Biaya Sewa Kontainer')->first();
                if ($biayaSewaKontainerCoa) {
                    $biayaSewaKontainerCoa->saldo += $difference;
                    $biayaSewaKontainerCoa->save();

                    Log::info('Biaya Sewa Kontainer COA Updated on Edit', [
                        'old_total' => $oldTotal,
                        'new_total' => $newTotal,
                        'difference' => $difference,
                        'new_balance' => $biayaSewaKontainerCoa->saldo
                    ]);
                }
            }

            $pembayaran->update([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'tanggal_kas' => $request->tanggal_kas,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'total_pembayaran' => $request->total_pembayaran,
                'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian ?? 0,
                'total_tagihan_setelah_penyesuaian' => $newTotal,
                'keterangan' => $request->keterangan,
                'diupdate_oleh' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('pembayaran-pranota-kontainer.index')
                ->with('success', 'Pembayaran berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate pembayaran: ' . $e->getMessage());
        }
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

            // Hapus transaksi COA berdasarkan nomor referensi
            $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran);

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

    /**
     * Generate nomor pembayaran untuk AJAX request
     */
    public function generateNomorPembayaran(Request $request)
    {
        try {
            $nomorCetakan = $request->get('nomor_cetakan', 1);
            $kodeBank = $request->get('kode_bank', '000');

            $nomorPembayaran = PembayaranPranotaKontainer::generateNomorPembayaran($nomorCetakan, $kodeBank);

            return response()->json([
                'success' => true,
                'nomor_pembayaran' => $nomorPembayaran
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available DP payments from pembayaran aktivitas lainnya
     */
    public function getAvailableDP()
    {
        try {
            // Get DP IDs that are already used in pembayaran pranota kontainer
            $usedDPIds = \App\Models\PembayaranPranotaKontainer::whereNotNull('dp_payment_id')
                ->pluck('dp_payment_id')
                ->toArray();

            // Import model PembayaranAktivitasLainnya and filter out used DPs
            $dpPayments = \App\Models\PembayaranAktivitasLainnya::where('is_dp', true)
                ->whereNotIn('id', $usedDPIds)
                ->with(['bank', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'nomor_pembayaran' => $payment->nomor_pembayaran,
                        'tanggal_pembayaran' => \Carbon\Carbon::parse($payment->tanggal_pembayaran)->format('d/m/Y'),
                        'total_pembayaran' => $payment->total_pembayaran,
                        'total_formatted' => 'Rp ' . number_format((float)$payment->total_pembayaran, 0, ',', '.'),
                        'bank_name' => $payment->bank->nama_akun ?? '-',
                        'aktivitas_pembayaran' => $payment->aktivitas_pembayaran,
                        'creator_name' => $payment->creator->username ?? '-',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $dpPayments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching DP payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment detail by nomor pembayaran (for transaction detail modal)
     */
    public function getDetailByNomor($nomorPembayaran)
    {
        try {
            $pembayaran = PembayaranPranotaKontainer::with([
                'pembuatPembayaran',
                'penyetujuPembayaran',
                'items.pranota'
            ])
            ->where('nomor_pembayaran', $nomorPembayaran)
            ->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            // Group pranota and collect tagihan
            $pranotaList = [];
            $tagihanList = [];
            $pranotaIds = [];

            foreach ($pembayaran->items as $item) {
                $pranota = $item->pranota;

                // Add pranota to list (avoid duplicates)
                if ($pranota && !in_array($pranota->id, $pranotaIds)) {
                    $pranotaIds[] = $pranota->id;

                    // Get tagihan items (tagihanKontainerSewaItems adalah method, bukan relasi)
                    $tagihanItems = $pranota->tagihanKontainerSewaItems();

                    // Count tagihan for this pranota
                    $jumlahTagihan = $tagihanItems->count();

                    // Calculate total amount for this pranota (gunakan 'grand_total' bukan 'total_biaya')
                    $totalAmount = $tagihanItems->sum('grand_total');

                    $pranotaList[] = [
                        'no_invoice' => $pranota->no_invoice,
                        'tanggal_pranota' => $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') : null,
                        'jumlah_tagihan' => $jumlahTagihan,
                        'total_amount' => $totalAmount
                    ];

                    // Collect all tagihan for this pranota
                    foreach ($tagihanItems as $tagihan) {
                        // Calculate lama hari from tanggal_awal and tanggal_akhir
                        $lamaHari = null;
                        if ($tagihan->tanggal_awal && $tagihan->tanggal_akhir) {
                            $startDate = \Carbon\Carbon::parse($tagihan->tanggal_awal);
                            $endDate = \Carbon\Carbon::parse($tagihan->tanggal_akhir);
                            $lamaHari = $startDate->diffInDays($endDate);
                        }

                        $tagihanList[] = [
                            'nomor_kontainer' => $tagihan->nomor_kontainer,
                            'ukuran_kontainer' => $tagihan->size ?? 'N/A', // Kolom 'size' bukan 'ukuran_kontainer'
                            'periode' => $tagihan->periode ?? '-',
                            'tanggal_mulai' => $tagihan->tanggal_awal ? \Carbon\Carbon::parse($tagihan->tanggal_awal)->format('d/m/Y') : null,
                            'tanggal_akhir' => $tagihan->tanggal_akhir ? \Carbon\Carbon::parse($tagihan->tanggal_akhir)->format('d/m/Y') : null,
                            'tarif' => $tagihan->tarif ?? 'N/A',
                            'lama_hari' => $lamaHari ?? 0,
                            'dpp' => $tagihan->dpp ?? 0,
                            'ppn' => $tagihan->ppn ?? 0,
                            'pph' => $tagihan->pph ?? 0,
                            'total_biaya' => $tagihan->grand_total ?? 0 // Kolom 'grand_total' bukan 'total_biaya'
                        ];
                    }
                }
            }

            $data = [
                'nomor_pembayaran' => $pembayaran->nomor_pembayaran,
                'tanggal_pembayaran' => \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y'),
                'bank' => $pembayaran->bank ?? '-', // bank adalah string nama akun, bukan relasi
                'total_tagihan_setelah_penyesuaian' => $pembayaran->total_tagihan_setelah_penyesuaian,
                'pranota_list' => $pranotaList,
                'tagihan_list' => $tagihanList
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail pembayaran'
            ], 500);
        }
    }
}
