<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaRit;
use App\Models\PranotaUangRit;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PembayaranPranotaRitController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-pranota-rit-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-rit-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-rit-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-rit-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PembayaranPranotaRit::with(['pranotaUangRits', 'createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                  ->orWhere('nomor_accurate', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(20);
        return view('pembayaran-pranota-rit.index', compact('items'));
    }

    public function create(Request $request)
    {
        // Only show approved pranota that hasn't been paid
        $pranotaQuery = PranotaUangRit::where('status', 'approved');

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

        return view('pembayaran-pranota-rit.create', compact('pranotaRits', 'nomorPembayaran', 'akunCoa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_uang_rit_ids' => ['required', 'array', 'min:1'],
            'pranota_uang_rit_ids.*' => ['exists:pranota_uang_rits,id'],
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
            unset($paymentData['pranota_uang_rit_ids']);
            
            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = 'paid';
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            $pembayaran = PembayaranPranotaRit::create($paymentData);

            foreach ($validated['pranota_uang_rit_ids'] as $pranotaId) {
                $pranota = PranotaUangRit::findOrFail($pranotaId);
                
                $subtotal = $pranota->grand_total_bersih;

                $pembayaran->pranotaUangRits()->attach($pranotaId, [
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $pranota->update([
                    'status' => 'paid',
                    'tanggal_bayar' => $validated['tanggal_pembayaran']
                ]);
            }

            // Accounting Entry (Double Book)
            $totalFinal = $validated['total_tagihan_setelah_penyesuaian'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $desc = "Pembayaran Pranota Rit - " . $nomorPembayaran;

            // Typically for "Rit", it might go to "Biaya Ritasi" or similar COA
            // Let's check if 'Biaya Ritasi' exists or use a generic one
            $costAccount = 'Biaya Ritasi';
            
            if ($jenisTransaksi == 'Debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    ['nama_akun' => $costAccount, 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Rit',
                    $desc
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $costAccount, 'jumlah' => $totalFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Rit',
                    $desc
                );
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-rit.index')
                           ->with('success', 'Pembayaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Rit: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = PembayaranPranotaRit::with(['pranotaUangRits.creator', 'createdBy'])->findOrFail($id);
        return view('pembayaran-pranota-rit.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = PembayaranPranotaRit::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Restore pranota status
            foreach($item->pranotaUangRits as $pranota) {
                $pranota->update([
                    'status' => 'approved',
                    'tanggal_bayar' => null
                ]);
            }

            // Delete accounting entries
            $this->coaTransactionService->deleteTransactionByReference($item->nomor_pembayaran);

            $item->delete();
            DB::commit();
            return redirect()->route('pembayaran-pranota-rit.index')->with('success', 'Pembayaran berhasil dihapus.');
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
        
        return "SIS-{$bulan}-{$tahun}-{$runningNumber}";
    }

    public function generateNomor()
    {
        return response()->json([
            'success' => true,
            'nomor_pembayaran' => $this->generateNomorPembayaranSIS()
        ]);
    }
}
