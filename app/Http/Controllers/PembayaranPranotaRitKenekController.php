<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaRitKenek;
use App\Models\PranotaUangRitKenek;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PembayaranPranotaRitKenekController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-rit-kenek-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-rit-kenek-create')->only(['create', 'store']);
        // Add more middlewares if needed
    }

    public function index(Request $request)
    {
        $query = PranotaUangRitKenek::with(['kenekDetails', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by no_pranota or kenek name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pranota', 'like', "%{$search}%")
                  ->orWhere('kenek_nama', 'like', "%{$search}%");
            });
        }

        $pranotaList = $query->orderBy('tanggal', 'desc')->paginate(15);

        $statuses = PranotaUangRitKenek::getStatusOptions();

        return view('pembayaran-pranota-rit-kenek.index', compact('pranotaList', 'statuses'));
    }

    public function create(Request $request)
    {
        // Show pranota that hasn't been paid (approved status is ready to be paid)
        $pranotaQuery = PranotaUangRitKenek::whereIn('status', ['draft', 'submitted', 'approved']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaQuery->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('pranota_id')) {
            $pranotaQuery->where('id', $request->pranota_id);
        }

        $pranotaRits = $pranotaQuery->orderBy('tanggal', 'desc')->get();

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();

        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-rit-kenek.create', compact('pranotaRits', 'nomorPembayaran', 'akunCoa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_uang_rit_kenek_ids' => ['required', 'array', 'min:1'],
            'pranota_uang_rit_kenek_ids.*' => ['exists:pranota_uang_rit_keneks,id'],
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
            unset($paymentData['pranota_uang_rit_kenek_ids']);
            
            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = 'paid';
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            $pembayaran = PembayaranPranotaRitKenek::create($paymentData);

            foreach ($validated['pranota_uang_rit_kenek_ids'] as $pranotaId) {
                $pranota = PranotaUangRitKenek::findOrFail($pranotaId);
                
                $subtotal = $pranota->grand_total_bersih;

                $pembayaran->pranotaUangRitKeneks()->attach($pranotaId, [
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $pranota->update([
                    'status' => PranotaUangRitKenek::STATUS_PAID,
                    'tanggal_bayar' => $validated['tanggal_pembayaran']
                ]);
            }

            // Accounting Entry
            $totalFinal = $validated['total_tagihan_setelah_penyesuaian'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $desc = "Pembayaran Pranota Rit Kenek - " . $nomorPembayaran;

            $costAccount = 'Biaya Ritasi'; // Same as supir
            
            if ($jenisTransaksi == 'Debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    ['nama_akun' => $costAccount, 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Rit Kenek',
                    $desc
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $costAccount, 'jumlah' => $totalFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Rit Kenek',
                    $desc
                );
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-rit-kenek.index')
                           ->with('success', 'Pembayaran Rit Kenek berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Rit Kenek: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = PembayaranPranotaRitKenek::with(['pranotaUangRitKeneks.creator', 'createdBy'])->findOrFail($id);
        return view('pembayaran-pranota-rit-kenek.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = PembayaranPranotaRitKenek::findOrFail($id);
        
        DB::beginTransaction();
        try {
            foreach($item->pranotaUangRitKeneks as $pranota) {
                $pranota->update([
                    'status' => PranotaUangRitKenek::STATUS_APPROVED,
                    'tanggal_bayar' => null
                ]);
            }

            $this->coaTransactionService->deleteTransactionByReference($item->nomor_pembayaran);

            $item->delete();
            DB::commit();
            return redirect()->route('pembayaran-pranota-rit-kenek.index')->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    private function generateNomorPembayaranSIS()
    {
        $modulSis = NomorTerakhir::where('modul', 'SIS')->first();
        if (!$modulSis) {
            $modulSis = NomorTerakhir::create(['modul' => 'SIS', 'nomor_terakhir' => 0]);
        }

        $now = now();
        $bulan = $now->format('m');
        $tahun = $now->format('y');
        $runningNumber = str_pad($modulSis->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT);
        
        return "SIS-K-{$bulan}-{$tahun}-{$runningNumber}"; // K for Kenek
    }
}
