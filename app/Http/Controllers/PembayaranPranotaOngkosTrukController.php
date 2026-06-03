<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Models\PembayaranPranotaOngkosTruk;
use App\Models\PranotaOngkosTruk;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PembayaranPranotaOngkosTrukController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-ongkos-truk-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-ongkos-truk-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-ongkos-truk-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-ongkos-truk-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PembayaranPranotaOngkosTruk::with(['pranotaOngkosTruks', 'createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                    ->orWhere('nomor_accurate', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(20);

        // Check if synced with COA
        $syncedReferences = \App\Models\CoaTransaction::whereIn('nomor_referensi', $items->pluck('nomor_pembayaran'))
            ->pluck('nomor_referensi')
            ->toArray();

        foreach ($items as $item) {
            $item->is_synced = in_array($item->nomor_pembayaran, $syncedReferences);
        }

        return view('pembayaran-pranota-ongkos-truk.index', compact('items'));
    }

    public function create(Request $request)
    {
        $pranotaQuery = PranotaOngkosTruk::where('status_pembayaran', 'unpaid')
            ->whereDoesntHave('pembayaranPranotaOngkosTruks');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaQuery->whereBetween('tanggal_pranota', [$request->start_date, $request->end_date]);
        }

        $pranotaOngkosTruks = $pranotaQuery->orderBy('tanggal_pranota', 'desc')->get();

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();

        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-ongkos-truk.create', compact('pranotaOngkosTruks', 'nomorPembayaran', 'akunCoa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_ongkos_truk_ids' => ['required', 'array', 'min:1'],
            'pranota_ongkos_truk_ids.*' => ['exists:pranota_ongkos_truks,id'],
            'nomor_pembayaran' => 'nullable|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['Debit', 'Kredit'])],
            'bank' => 'required|string|max:255',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $modulSis = NomorTerakhir::where('modul', 'SIS')->firstOrCreate(
                ['modul' => 'SIS'],
                ['nomor_terakhir' => 0, 'keterangan' => 'SIS Modul']
            );

            $nomorPembayaran = $this->generateNomorPembayaranSIS();
            $modulSis->increment('nomor_terakhir');

            $paymentData = $validated;
            unset($paymentData['pranota_ongkos_truk_ids']);

            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = 'paid';
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            $pembayaran = PembayaranPranotaOngkosTruk::create($paymentData);

            foreach ($validated['pranota_ongkos_truk_ids'] as $pranotaId) {
                $pranota = PranotaOngkosTruk::findOrFail($pranotaId);

                $pembayaran->pranotaOngkosTruks()->attach($pranotaId, [
                    'subtotal' => $pranota->total_nominal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $pranota->update([
                    'status_pembayaran' => 'paid',
                    'status' => 'paid',
                ]);
            }

            // Accounting Entry (Double Book)
            $totalFinal = $validated['total_tagihan_setelah_penyesuaian'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $desc = 'Pembayaran Pranota Ongkos Truk - '.$nomorPembayaran;

            if ($jenisTransaksi == 'Debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    ['nama_akun' => 'Biaya Trucking', 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Ongkos Truk',
                    $desc
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Trucking', 'jumlah' => $totalFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Ongkos Truk',
                    $desc
                );
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-ongkos-truk.index')
                ->with('success', 'Pembayaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Ongkos Truk: '.$e->getMessage());

            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        $item = PembayaranPranotaOngkosTruk::with('pranotaOngkosTruks')->findOrFail($id);

        if ($item->isCancelled()) {
            return redirect()->route('pembayaran-pranota-ongkos-truk.index')
                ->with('error', 'Pembayaran yang dibatalkan tidak dapat diubah.');
        }

        $selectedPranotaIds = $item->pranotaOngkosTruks->pluck('id')->toArray();

        $pranotaOngkosTruks = PranotaOngkosTruk::where(function ($query) {
            $query->where('status_pembayaran', 'unpaid')
                ->whereDoesntHave('pembayaranPranotaOngkosTruks');
        })
            ->orWhereIn('id', $selectedPranotaIds)
            ->orderBy('tanggal_pranota', 'desc')
            ->get();

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();

        return view('pembayaran-pranota-ongkos-truk.edit', compact('item', 'pranotaOngkosTruks', 'akunCoa', 'selectedPranotaIds'));
    }

    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranPranotaOngkosTruk::findOrFail($id);

        if ($pembayaran->isCancelled()) {
            return redirect()->route('pembayaran-pranota-ongkos-truk.index')
                ->with('error', 'Pembayaran yang dibatalkan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'pranota_ongkos_truk_ids' => ['required', 'array', 'min:1'],
            'pranota_ongkos_truk_ids.*' => ['exists:pranota_ongkos_truks,id'],
            'nomor_pembayaran' => 'required|string',
            'nomor_accurate' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['Debit', 'Kredit'])],
            'bank' => 'required|string|max:255',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Restore old pranota statuses
            foreach ($pembayaran->pranotaOngkosTruks as $oldPranota) {
                $oldPranota->update([
                    'status_pembayaran' => 'unpaid',
                    'status' => 'submitted',
                ]);
            }

            // Revert accounting
            $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran);

            // Update main record
            $paymentData = $validated;
            unset($paymentData['pranota_ongkos_truk_ids']);
            $paymentData['updated_by'] = Auth::id();

            $pembayaran->update($paymentData);

            // Sync pranotas
            $syncData = [];
            foreach ($validated['pranota_ongkos_truk_ids'] as $pranotaId) {
                $pranota = PranotaOngkosTruk::findOrFail($pranotaId);

                $syncData[$pranotaId] = [
                    'subtotal' => $pranota->total_nominal,
                    'updated_at' => now(),
                ];

                $pranota->update([
                    'status_pembayaran' => 'paid',
                    'status' => 'paid',
                ]);
            }

            $pembayaran->pranotaOngkosTruks()->sync($syncData);

            // Accounting Entry (Double Book)
            $totalFinal = $validated['total_tagihan_setelah_penyesuaian'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $desc = 'Update Pembayaran Pranota Ongkos Truk - '.$pembayaran->nomor_pembayaran;

            if ($jenisTransaksi == 'Debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    ['nama_akun' => 'Biaya Trucking', 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $pembayaran->nomor_pembayaran,
                    'Pembayaran Pranota Ongkos Truk',
                    $desc
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Trucking', 'jumlah' => $totalFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $pembayaran->nomor_pembayaran,
                    'Pembayaran Pranota Ongkos Truk',
                    $desc
                );
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-ongkos-truk.index')
                ->with('success', 'Pembayaran berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating Pembayaran Pranota Ongkos Truk: '.$e->getMessage());

            return back()->withInput()->with('error', 'Gagal update pembayaran: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $item = PembayaranPranotaOngkosTruk::with(['pranotaOngkosTruks.creator', 'createdBy'])->findOrFail($id);

        return view('pembayaran-pranota-ongkos-truk.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = PembayaranPranotaOngkosTruk::findOrFail($id);

        DB::beginTransaction();
        try {
            // Restore pranota statuses
            foreach ($item->pranotaOngkosTruks as $pranota) {
                $pranota->update([
                    'status_pembayaran' => 'unpaid',
                    'status' => 'submitted',
                ]);
            }

            // Delete accounting entries
            $this->coaTransactionService->deleteTransactionByReference($item->nomor_pembayaran);

            $item->delete();
            DB::commit();

            return redirect()->route('pembayaran-pranota-ongkos-truk.index')->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus: '.$e->getMessage());
        }
    }

    private function generateNomorPembayaranSIS()
    {
        $modulSis = NomorTerakhir::firstOrCreate(
            ['modul' => 'SIS'],
            ['nomor_terakhir' => 0, 'keterangan' => 'SIS Modul']
        );

        $now = now();
        $bulan = $now->format('m');
        $tahun = $now->format('y');
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);

        return "SIS-{$bulan}-{$tahun}-{$runningNumber}";
    }

    public function generateNomor()
    {
        return response()->json([
            'success' => true,
            'nomor_pembayaran' => $this->generateNomorPembayaranSIS(),
        ]);
    }

    public function syncToCoa(PembayaranPranotaOngkosTruk $pembayaran)
    {
        DB::beginTransaction();
        try {
            // Check if transaction already exists in COA
            $existing = \App\Models\CoaTransaction::where('nomor_referensi', $pembayaran->nomor_pembayaran)->first();
            if ($existing) {
                return back()->with('error', 'Data ini sudah ada di jurnal COA.');
            }

            $totalFinal = $pembayaran->total_tagihan_setelah_penyesuaian;
            $bankName = $pembayaran->bank;
            $jenisTransaksi = $pembayaran->jenis_transaksi;
            $desc = 'Pembayaran Pranota Ongkos Truk - '.$pembayaran->nomor_pembayaran.' (Synced)';

            if ($jenisTransaksi == 'Debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    ['nama_akun' => 'Biaya Trucking', 'jumlah' => $totalFinal],
                    $pembayaran->tanggal_pembayaran,
                    $pembayaran->nomor_pembayaran,
                    'Pembayaran Pranota Ongkos Truk',
                    $desc
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Trucking', 'jumlah' => $totalFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    $pembayaran->tanggal_pembayaran,
                    $pembayaran->nomor_pembayaran,
                    'Pembayaran Pranota Ongkos Truk',
                    $desc
                );
            }

            DB::commit();

            return back()->with('success', 'Data berhasil disinkronkan ke jurnal COA.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncToCoa POT: '.$e->getMessage());

            return back()->with('error', 'Gagal sinkronisasi: '.$e->getMessage());
        }
    }
}
