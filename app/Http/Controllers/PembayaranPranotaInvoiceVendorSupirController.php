<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\PembayaranPranotaVendorSupir;
use App\Models\PembayaranPranotaVendorSupirItem;
use App\Models\PranotaInvoiceVendorSupir;
use App\Models\VendorSupir; // Added Coa model
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranPranotaInvoiceVendorSupirController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        // Permissions will be added later if needed, for now use standard auth
    }

    public function index(Request $request)
    {
        $query = PembayaranPranotaVendorSupir::with(['vendor', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                    ->orWhere('nomor_accurate', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vendorQ) use ($search) {
                        $vendorQ->where('nama_vendor', 'like', "%{$search}%");
                    });
            });
        }

        $pembayarans = $query->latest()->paginate(15);

        // Check which payments are already in COA
        $syncedReferences = \App\Models\CoaTransaction::whereIn('nomor_referensi', $pembayarans->pluck('nomor_pembayaran'))
            ->pluck('nomor_referensi')
            ->toArray();

        foreach ($pembayarans as $pembayaran) {
            $pembayaran->is_synced = in_array($pembayaran->nomor_pembayaran, $syncedReferences);
        }

        return view('pembayaran-pranota-invoice-vendor-supir.index', compact('pembayarans'));
    }

    public function create(Request $request)
    {
        $vendors = VendorSupir::orderBy('nama_vendor')->get();

        $selectedVendorId = $request->vendor_id;
        $pranotas = [];

        if ($selectedVendorId) {
            $pranotas = PranotaInvoiceVendorSupir::where('vendor_id', $selectedVendorId)
                ->where('status_pembayaran', '!=', 'lunas')
                ->orderBy('tanggal_pranota', 'desc')
                ->get();
        }

        // Get akun COA for bank selection
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();

        // Generate nomor pembayaran
        $lastPayment = PembayaranPranotaVendorSupir::latest()->first();
        $nextId = $lastPayment ? $lastPayment->id + 1 : 1;
        $nomorPembayaran = 'PAY-VS-'.date('Ymd').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('pembayaran-pranota-invoice-vendor-supir.create', compact('vendors', 'pranotas', 'selectedVendorId', 'nomorPembayaran', 'akunCoa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pembayaran' => 'required|unique:pembayaran_pranota_vendor_supirs',
            'tanggal_pembayaran' => 'required|date',
            'vendor_id' => 'required|exists:vendor_supirs,id',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'metode_pembayaran' => 'required|string',
            'pranota_ids' => 'required|array|min:1',
            'nominal_bayar' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $pembayaran = PembayaranPranotaVendorSupir::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'vendor_id' => $request->vendor_id,
                'total_pembayaran' => $request->total_pembayaran,
                'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian,
                'total_tagihan_setelah_penyesuaian' => $request->total_tagihan_setelah_penyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bank' => $request->bank,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->pranota_ids as $pranotaId) {
                $nominal = $request->nominal_bayar[$pranotaId] ?? 0;
                if ($nominal > 0) {
                    PembayaranPranotaVendorSupirItem::create([
                        'pembayaran_id' => $pembayaran->id,
                        'pranota_id' => $pranotaId,
                        'nominal' => $nominal,
                    ]);

                    // Update Pranota Status
                    $pranota = PranotaInvoiceVendorSupir::with('invoiceTagihanVendors.tagihanSupirVendors')->find($pranotaId);
                    $totalTelahDibayar = PembayaranPranotaVendorSupirItem::where('pranota_id', $pranotaId)->sum('nominal');

                    if ($totalTelahDibayar >= ($pranota->grand_total ?? $pranota->total_nominal)) {
                        $newStatus = 'lunas';
                    } else {
                        $newStatus = 'sebagian';
                    }
                    $pranota->status_pembayaran = $newStatus;
                    $pranota->save();

                    // Cascade status to children
                    if ($pranota->invoiceTagihanVendors) {
                        foreach ($pranota->invoiceTagihanVendors as $invoice) {
                            $invoice->status_pembayaran = $newStatus;
                            $invoice->save();

                            if ($invoice->tagihanSupirVendors) {
                                foreach ($invoice->tagihanSupirVendors as $tagihan) {
                                    $tagihan->status_pembayaran = $newStatus;
                                    $tagihan->save();
                                }
                            }
                        }
                    }
                }
            }

            // Catat transaksi menggunakan double-entry COA (Double Book)
            $totalPembayaranFinal = $request->total_tagihan_setelah_penyesuaian;
            $bankName = $request->bank;
            $metodePembayaran = $request->metode_pembayaran; // di view ini 'debit' atau 'kredit'
            $keteranganAccounting = 'Pembayaran Pranota Vendor - '.$request->nomor_pembayaran;

            // Tentukan apakah bank di-debit atau di-kredit
            // Note: Untuk vendor supir, akun bebannya adalah "Biaya Uang Jalan Vendor"
            if (strtolower($metodePembayaran) == 'debit') {
                // Debit: Bank bertambah (Dr), Biaya berkurang (Cr)
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaranFinal],
                    ['nama_akun' => 'Biaya Uang Jalan Vendor', 'jumlah' => $totalPembayaranFinal],
                    $request->tanggal_pembayaran,
                    $request->nomor_pembayaran,
                    'Pembayaran Pranota Invoice Vendor',
                    $keteranganAccounting
                );
            } else {
                // Kredit: Biaya bertambah (Dr), Bank berkurang (Cr)
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Uang Jalan Vendor', 'jumlah' => $totalPembayaranFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaranFinal],
                    $request->tanggal_pembayaran,
                    $request->nomor_pembayaran,
                    'Pembayaran Pranota Invoice Vendor',
                    $keteranganAccounting
                );
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-invoice-vendor-supir.index')->with('success', 'Pembayaran berhasil disimpan dan dicatat ke accounting.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store pembayaran: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage())->withInput();
        }
    }

    public function show($pembayaran)
    {
        $pembayaran = PembayaranPranotaVendorSupir::with(['vendor', 'items.pranota', 'creator'])->findOrFail($pembayaran);

        return view('pembayaran-pranota-invoice-vendor-supir.show', compact('pembayaran'));
    }

    public function edit(PembayaranPranotaVendorSupir $pembayaran)
    {
        $pembayaran->load('items.pranota');
        $vendors = VendorSupir::orderBy('nama_vendor')->get();
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')->get();
        $akunBiaya = Coa::orderBy('nomor_akun')->get();
        $pranotas = PranotaInvoiceVendorSupir::where('vendor_id', $pembayaran->vendor_id)
            ->where(function ($query) use ($pembayaran) {
                $query->where('status_pembayaran', '!=', 'lunas')
                    ->orWhereIn('id', $pembayaran->items->pluck('pranota_id'));
            })->orderBy('tanggal_pranota', 'desc')->get();
        $selectedItems = $pembayaran->items->keyBy('pranota_id');

        return view('pembayaran-pranota-invoice-vendor-supir.edit', compact('pembayaran', 'vendors', 'akunCoa', 'akunBiaya', 'pranotas', 'selectedItems'));
    }

    public function update(Request $request, PembayaranPranotaVendorSupir $pembayaran)
    {
        $data = $request->validate([
            'tanggal_pembayaran' => 'required|date', 'nomor_accurate' => 'nullable|string|max:255',
            'bank' => 'required|string|max:255', 'metode_pembayaran' => 'required|in:debit,kredit',
            'akun_biaya_coa_id' => 'required|exists:akun_coa,id',
            'total_tagihan_penyesuaian' => 'nullable|numeric', 'alasan_penyesuaian' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string', 'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'required|integer|distinct|exists:pranota_invoice_vendor_supirs,id',
            'nominal_bayar' => 'required|array', 'nominal_bayar.*' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $oldPranotaIds = $pembayaran->items()->pluck('pranota_id');
            if (! $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran)) {
                throw new \RuntimeException('Jurnal COA lama gagal dibatalkan.');
            }
            $pembayaran->items()->delete();
            $total = 0;
            foreach ($data['pranota_ids'] as $pranotaId) {
                $nominal = (float) $data['nominal_bayar'][$pranotaId];
                PembayaranPranotaVendorSupirItem::create(['pembayaran_id' => $pembayaran->id, 'pranota_id' => $pranotaId, 'nominal' => $nominal]);
                $total += $nominal;
            }
            $adjustment = (float) ($data['total_tagihan_penyesuaian'] ?? 0);
            $finalTotal = $total + $adjustment;
            if ($finalTotal <= 0) throw new \RuntimeException('Total pembayaran setelah penyesuaian harus lebih dari nol.');
            $pembayaran->update([
                'tanggal_pembayaran' => $data['tanggal_pembayaran'], 'nomor_accurate' => $data['nomor_accurate'] ?? null,
                'bank' => $data['bank'], 'metode_pembayaran' => $data['metode_pembayaran'], 'total_pembayaran' => $total,
                'akun_biaya_coa_id' => $data['akun_biaya_coa_id'],
                'total_tagihan_penyesuaian' => $adjustment, 'total_tagihan_setelah_penyesuaian' => $finalTotal,
                'alasan_penyesuaian' => $data['alasan_penyesuaian'] ?? null, 'keterangan' => $data['keterangan'] ?? null,
                'updated_by' => Auth::id(),
            ]);
            foreach ($oldPranotaIds->merge($data['pranota_ids'])->unique() as $pranotaId) {
                $pranota = PranotaInvoiceVendorSupir::with('invoiceTagihanVendors.tagihanSupirVendors')->find($pranotaId);
                if (! $pranota) continue;
                $paid = (float) PembayaranPranotaVendorSupirItem::where('pranota_id', $pranotaId)->sum('nominal');
                $status = $paid >= (float) ($pranota->grand_total ?? $pranota->total_nominal) ? 'lunas' : ($paid > 0 ? 'sebagian' : 'belum lunas');
                $pranota->update(['status_pembayaran' => $status]);
                foreach ($pranota->invoiceTagihanVendors ?? [] as $invoice) {
                    $invoice->update(['status_pembayaran' => $status]);
                    foreach ($invoice->tagihanSupirVendors ?? [] as $tagihan) $tagihan->update(['status_pembayaran' => $status]);
                }
            }
            $debit = strtolower($data['metode_pembayaran']) === 'debit';
            $akunBiaya = Coa::findOrFail($data['akun_biaya_coa_id']);
            $entries = $debit
                ? [['nama_akun' => $data['bank'], 'jumlah' => $finalTotal], ['nama_akun' => $akunBiaya->nama_akun, 'coa_id' => $akunBiaya->id, 'jumlah' => $finalTotal]]
                : [['nama_akun' => $akunBiaya->nama_akun, 'coa_id' => $akunBiaya->id, 'jumlah' => $finalTotal], ['nama_akun' => $data['bank'], 'jumlah' => $finalTotal]];
            if (! $this->coaTransactionService->recordDoubleEntry($entries[0], $entries[1], $data['tanggal_pembayaran'], $pembayaran->nomor_pembayaran, 'Pembayaran Pranota Invoice Vendor', 'Pembayaran Pranota Vendor - '.$pembayaran->nomor_pembayaran)) {
                throw new \RuntimeException('Jurnal COA gagal dibuat. Periksa akun bank dan akun biaya.');
            }
            DB::commit();
            return redirect()->route('pembayaran-pranota-invoice-vendor-supir.index')->with('success', 'Pembayaran berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error update pembayaran vendor supir', ['id' => $pembayaran->id, 'error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal memperbarui pembayaran: '.$e->getMessage());
        }
    }

    public function updateAccurate(Request $request, $pembayaran)
    {
        $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
        ]);

        $pembayaran = PembayaranPranotaVendorSupir::findOrFail($pembayaran);
        $pembayaran->nomor_accurate = $request->nomor_accurate;
        $pembayaran->save();

        return redirect()->back()->with('success', 'Nomor Accurate berhasil diperbarui');
    }

    public function syncToCoa(PembayaranPranotaVendorSupir $pembayaran)
    {
        DB::beginTransaction();
        try {
            // Check if transaction already exists in COA
            $existing = \App\Models\CoaTransaction::where('nomor_referensi', $pembayaran->nomor_pembayaran)->first();
            if ($existing) {
                return back()->with('error', 'Data ini sudah ada di jurnal COA.');
            }

            $totalPembayaranFinal = $pembayaran->total_tagihan_setelah_penyesuaian;
            $bankName = $pembayaran->bank;
            $metodePembayaran = strtolower($pembayaran->metode_pembayaran);
            $keteranganAccounting = 'Pembayaran Pranota Vendor - '.$pembayaran->nomor_pembayaran;

            if ($metodePembayaran == 'debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaranFinal],
                    ['nama_akun' => 'Biaya Uang Jalan Vendor', 'jumlah' => $totalPembayaranFinal],
                    $pembayaran->tanggal_pembayaran,
                    $pembayaran->nomor_pembayaran,
                    'Pembayaran Pranota Invoice Vendor',
                    $keteranganAccounting
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Uang Jalan Vendor', 'jumlah' => $totalPembayaranFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaranFinal],
                    $pembayaran->tanggal_pembayaran,
                    $pembayaran->nomor_pembayaran,
                    'Pembayaran Pranota Invoice Vendor',
                    $keteranganAccounting
                );
            }

            DB::commit();

            return back()->with('success', 'Data berhasil disinkronkan ke jurnal COA.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncToCoa: '.$e->getMessage());

            return back()->with('error', 'Gagal sinkronisasi: '.$e->getMessage());
        }
    }

    public function destroy($pembayaran)
    {
        DB::beginTransaction();
        try {
            $pembayaranRecord = PembayaranPranotaVendorSupir::with('items')->findOrFail($pembayaran);

            // Revert pranota status
            foreach ($pembayaranRecord->items as $item) {
                $pranotaId = $item->pranota_id;

                // Recalculate status pranota after this item is gone
                $pranota = PranotaInvoiceVendorSupir::with('invoiceTagihanVendors.tagihanSupirVendors')->find($pranotaId);

                // We use a temporary delete or sub calculation
                $totalTelahDibayarLainnya = PembayaranPranotaVendorSupirItem::where('pranota_id', $pranotaId)
                    ->where('id', '!=', $item->id)
                    ->sum('nominal');

                if ($totalTelahDibayarLainnya <= 0) {
                    $newStatus = 'belum_dibayar';
                } elseif ($totalTelahDibayarLainnya < ($pranota->grand_total ?? $pranota->total_nominal)) {
                    $newStatus = 'sebagian';
                } else {
                    $newStatus = 'lunas';
                }
                $pranota->status_pembayaran = $newStatus;
                $pranota->save();

                // Cascade status to children
                if ($pranota->invoiceTagihanVendors) {
                    foreach ($pranota->invoiceTagihanVendors as $invoice) {
                        $invoice->status_pembayaran = $newStatus;
                        $invoice->save();

                        if ($invoice->tagihanSupirVendors) {
                            foreach ($invoice->tagihanSupirVendors as $tagihan) {
                                $tagihan->status_pembayaran = $newStatus;
                                $tagihan->save();
                            }
                        }
                    }
                }

                $item->delete();
            }

            // Delete associated COA transactions
            if ($pembayaranRecord->nomor_pembayaran) {
                $this->coaTransactionService->deleteTransactionByReference($pembayaranRecord->nomor_pembayaran);
            }

            $pembayaranRecord->delete();
            DB::commit();

            return redirect()->route('pembayaran-pranota-invoice-vendor-supir.index')->with('success', 'Pembayaran berhasil dihapus dan jurnal COA telah dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error destroy pembayaran: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus: '.$e->getMessage());
        }
    }
}
