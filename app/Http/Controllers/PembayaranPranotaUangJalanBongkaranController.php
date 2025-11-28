<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaUangJalanBongkaran;
use App\Models\PranotaUangJalanBongkaran;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Models\SuratJalanBongkaran;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranPranotaUangJalanBongkaranController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PranotaUangJalanBongkaran::with(['pembayaranPranotaUangJalanBongkarans','creator', 'updater','uangJalanBongkarans']);

        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_pranota', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhereHas('uangJalanBongkarans', function($sq) use ($search) {
                      $sq->where('supir', 'like', "%{$search}%");
                  });
            });
        }

        $pranotaList = $query->orderBy('tanggal_pranota', 'desc')->paginate(15);

        $statuses = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            'cancelled' => 'Dibatalkan'
        ];

        return view('pembayaran-pranota-uang-jalan-bongkaran.index', compact('pranotaList', 'statuses'));
    }

    public function create(Request $request)
    {
        $pranotaQuery = PranotaUangJalanBongkaran::query();
        $pranotaQuery->where('status_pembayaran', 'unpaid')
            ->whereDoesntHave('pembayaranPranotaUangJalanBongkarans');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaQuery->whereBetween('tanggal_pranota', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('pranota_id')) {
            $pranotaQuery->where('id', $request->pranota_id);
        }

        $pranotaUangJalans = $pranotaQuery->with(['uangJalanBongkarans.suratJalanBongkaran'])
            ->orderBy('tanggal_pranota', 'desc')
            ->get();

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-uang-jalan-bongkaran.create', compact('pranotaUangJalans','nomorPembayaran','akunCoa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_uang_jalan_bongkaran_ids' => ['required', 'array', 'min:1'],
            'pranota_uang_jalan_bongkaran_ids.*' => ['exists:pranota_uang_jalan_bongkarans,id'],
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
            'nomor_cetakan' => 'nullable|integer|min:1|max:9'
        ]);

        DB::beginTransaction();

        try {
            $modulSis = NomorTerakhir::where('modul','SIS')->firstOrFail();
            $nomorPembayaran = $this->generateNomorPembayaranSIS();
            $modulSis->increment('nomor_terakhir');

            $paymentData = $validated;
            unset($paymentData['pranota_uang_jalan_bongkaran_ids']);
            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = PembayaranPranotaUangJalanBongkaran::STATUS_PAID ?? 'paid';
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            // Set primary pranota id on payment to first selected pranota
            $firstPranotaId = $validated['pranota_uang_jalan_bongkaran_ids'][0] ?? null;
            $paymentData['pranota_uang_jalan_bongkaran_id'] = $firstPranotaId;

            $pembayaran = PembayaranPranotaUangJalanBongkaran::create($paymentData);

            foreach ($validated['pranota_uang_jalan_bongkaran_ids'] as $pranotaId) {
                $pranota = PranotaUangJalanBongkaran::with(['uangJalanBongkarans'])->findOrFail($pranotaId);
                // Use items pivot table for records
                DB::table('pembayaran_pranota_uang_jalan_bongkaran_items')->insert([
                    'pembayaran_pranota_uang_jalan_bongkaran_id' => $pembayaran->id,
                    'pranota_uang_jalan_bongkaran_id' => $pranotaId,
                    'subtotal' => $pranota->total_for_payment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $pranota->update([
                    'status_pembayaran' => PranotaUangJalanBongkaran::STATUS_PAID,
                    'updated_by' => Auth::id()
                ]);

                // Update related uang jalan bongkaran status
                foreach ($pranota->uangJalanBongkarans as $uangJalan) {
                    $uangJalan->update([
                        'status' => 'lunas',
                        'updated_by' => Auth::id()
                    ]);

                    if ($uangJalan->suratJalanBongkaran) {
                        $uangJalan->suratJalanBongkaran->update([
                            'status_pembayaran_uang_jalan' => 'dibayar',
                            'status' => 'belum masuk checkpoint',
                            'updated_by' => Auth::id()
                        ]);
                    }
                }
            }

            // Accounting double entry
            $totalPembayaran = $validated['total_tagihan_setelah_penyesuaian'] ?? $validated['total_pembayaran'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $keterangan = 'Pembayaran Pranota Uang Jalan Bongkaran - ' . $validated['nomor_pembayaran'];

            if ($jenisTransaksi == 'Debit') {
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    ['nama_akun' => 'Biaya Uang Jalan Bongkar', 'jumlah' => $totalPembayaran],
                    $validated['tanggal_pembayaran'],
                    $validated['nomor_pembayaran'],
                    'Pembayaran Pranota Uang Jalan Bongkaran',
                    $keterangan
                );
            } else {
                $doubleEntryResult = $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Uang Jalan Bongkar', 'jumlah' => $totalPembayaran],
                    ['nama_akun' => $bankName, 'jumlah' => $totalPembayaran],
                    $validated['tanggal_pembayaran'],
                    $validated['nomor_pembayaran'],
                    'Pembayaran Pranota Uang Jalan Bongkaran',
                    $keterangan
                );
            }

            Log::info('Pembayaran Pranota Uang Jalan Bongkaran created', ['pembayaran' => $pembayaran->id, 'total' => $totalPembayaran]);

            DB::commit();

            $pranotaCount = count($validated['pranota_uang_jalan_bongkaran_ids']);
            $successMessage = "Pembayaran {$nomorPembayaran} berhasil disimpan untuk {$pranotaCount} pranota bongkaran.";

            return redirect()->route('pembayaran-pranota-uang-jalan-bongkaran.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating pembayaran pranota uang jalan bongkaran: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'data' => $validated]);
            $errorMessage = 'Gagal menyimpan pembayaran bongkaran. ' . $e->getMessage();
            return back()->withInput()->with('error', $errorMessage);
        }
    }

    public function show(PembayaranPranotaUangJalanBongkaran $pembayaran)
    {
        $pembayaran->load(['pranotaUangJalanBongkaran', 'items']);
        return view('pembayaran-pranota-uang-jalan-bongkaran.show', compact('pembayaran'));
    }

    public function edit(PembayaranPranotaUangJalanBongkaran $pembayaran)
    {
        if (isset($pembayaran->status_pembayaran) && ($pembayaran->status_pembayaran == 'paid' || $pembayaran->status_pembayaran == 'cancelled')) {
            return redirect()->route('pembayaran-pranota-uang-jalan-bongkaran.index')->with('error', 'Pembayaran yang sudah lunas atau dibatalkan tidak dapat diubah.');
        }
        return view('pembayaran-pranota-uang-jalan-bongkaran.edit', compact('pembayaran'));
    }

    public function update(Request $request, PembayaranPranotaUangJalanBongkaran $pembayaran)
    {
        if ($pembayaran->status_pembayaran == 'paid' || $pembayaran->status_pembayaran == 'cancelled') {
            return redirect()->route('pembayaran-pranota-uang-jalan-bongkaran.index')->with('error', 'Pembayaran yang sudah lunas atau dibatalkan tidak dapat diubah.');
        }

        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['cash', 'transfer', 'check', 'giro'])],
            'total_pembayaran' => 'required|numeric|min:0',
            'bank' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'jenis_transaksi' => $request->jenis_transaksi,
                'total_pembayaran' => $request->total_pembayaran,
                'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian ?? 0,
                'total_tagihan_setelah_penyesuaian' => $request->total_pembayaran,
                'bank' => $request->bank,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ];

            if ($request->hasFile('bukti_pembayaran')) {
                if ($pembayaran->bukti_pembayaran) {
                    Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
                }
                $file = $request->file('bukti_pembayaran');
                $filename = 'bukti_pembayaran_bongkaran_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
                $data['bukti_pembayaran'] = $path;
            }

            $pembayaran->update($data);
            DB::commit();

            return redirect()->route('pembayaran-pranota-uang-jalan-bongkaran.index')->with('success', 'Pembayaran bongkaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pembayaran bongkaran: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui pembayaran: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PembayaranPranotaUangJalanBongkaran $pembayaran)
    {
        if ($pembayaran->status_pembayaran == 'paid') {
            return redirect()->route('pembayaran-pranota-uang-jalan-bongkaran.index')->with('error', 'Pembayaran yang sudah lunas tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            if ($pembayaran->bukti_pembayaran) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }
            // Revert pranota states
            $items = DB::table('pembayaran_pranota_uang_jalan_bongkaran_items')->where('pembayaran_pranota_uang_jalan_bongkaran_id', $pembayaran->id)->get();
            foreach ($items as $item) {
                $pranota = PranotaUangJalanBongkaran::find($item->pranota_uang_jalan_bongkaran_id);
                if ($pranota) {
                    $pranota->update(['status_pembayaran' => 'unpaid']);
                }
            }
            DB::table('pembayaran_pranota_uang_jalan_bongkaran_items')->where('pembayaran_pranota_uang_jalan_bongkaran_id', $pembayaran->id)->delete();
            $pembayaran->delete();
            DB::commit();
            return redirect()->route('pembayaran-pranota-uang-jalan-bongkaran.index')->with('success', 'Pembayaran bongkaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembayaran bongkaran: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pembayaran: ' . $e->getMessage());
        }
    }

    private function generateNomorPembayaranSIS()
    {
        $modulSis = NomorTerakhir::firstOrCreate(['modul' => 'SIS'], ['nomor_terakhir' => 0, 'keterangan' => 'Nomor Pembayaran Pranota Uang Jalan Bongkaran']);
        $now = now();
        $bulan = $now->format('m');
        $tahun = $now->format('y');
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);
        return "SIS-{$bulan}-{$tahun}-{$runningNumber}";
    }

    public function generateNomor()
    {
        try {
            $nomor = $this->generateNomorPembayaranSIS();
            return response()->json(['success' => true, 'nomor_pembayaran' => $nomor]);
        } catch (\Exception $e) {
            Log::error('Error generating nomor pembayaran bongkaran: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal generate nomor pembayaran'], 500);
        }
    }
}
