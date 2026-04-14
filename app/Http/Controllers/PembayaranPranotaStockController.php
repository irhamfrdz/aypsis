<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaStock;
use App\Models\PranotaStock;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PembayaranPranotaStockController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        // We'll use the same permissions or define new ones if needed, 
        // but for now let's assume standard names for stock payments
        $this->middleware('can:pembayaran-pranota-stock-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-pranota-stock-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-pranota-stock-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-pranota-stock-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PembayaranPranotaStock::with(['pranotaStocks', 'createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                  ->orWhere('nomor_accurate', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('tanggal_pembayaran', 'desc')->paginate(20);
        return view('pembayaran-pranota-stock.index', compact('items'));
    }

    public function create(Request $request)
    {
        $pranotaQuery = PranotaStock::where('status', 'approved')
            ->whereDoesntHave('pembayaranPranotaStocks');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $pranotaQuery->whereBetween('tanggal_pranota', [$request->start_date, $request->end_date]);
        }

        $pranotaStocks = $pranotaQuery->orderBy('tanggal_pranota', 'desc')->get();

        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%bank%')
            ->orWhere('nama_akun', 'LIKE', '%kas%')
            ->orderBy('nama_akun')
            ->get();

        $nomorPembayaran = $this->generateNomorPembayaranSIS();

        return view('pembayaran-pranota-stock.create', compact('pranotaStocks', 'nomorPembayaran', 'akunCoa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pranota_stock_ids' => ['required', 'array', 'min:1'],
            'pranota_stock_ids.*' => ['exists:pranota_stocks,id'],
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
            unset($paymentData['pranota_stock_ids']);
            
            $paymentData['nomor_pembayaran'] = $nomorPembayaran;
            $paymentData['status_pembayaran'] = 'paid';
            $paymentData['created_by'] = Auth::id();
            $paymentData['updated_by'] = Auth::id();

            $pembayaran = PembayaranPranotaStock::create($paymentData);

            foreach ($validated['pranota_stock_ids'] as $pranotaId) {
                $pranota = PranotaStock::findOrFail($pranotaId);
                
                // Calculate subtotal for this pranota
                $subtotal = 0;
                if (is_array($pranota->items)) {
                    foreach ($pranota->items as $item) {
                        $subtotal += ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
                    }
                }
                $subtotal += $pranota->adjustment ?? 0;

                $pembayaran->pranotaStocks()->attach($pranotaId, [
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $pranota->update([
                    'status' => 'paid',
                ]);
            }

            // Accounting Entry (Double Book)
            $totalFinal = $validated['total_tagihan_setelah_penyesuaian'];
            $bankName = $validated['bank'];
            $jenisTransaksi = $validated['jenis_transaksi'];
            $desc = "Pembayaran Pranota Stock - " . $nomorPembayaran;

            if ($jenisTransaksi == 'Debit') {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    ['nama_akun' => 'Biaya Amprahan', 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Stock',
                    $desc
                );
            } else {
                $this->coaTransactionService->recordDoubleEntry(
                    ['nama_akun' => 'Biaya Amprahan', 'jumlah' => $totalFinal],
                    ['nama_akun' => $bankName, 'jumlah' => $totalFinal],
                    $validated['tanggal_pembayaran'],
                    $nomorPembayaran,
                    'Pembayaran Pranota Stock',
                    $desc
                );
            }

            DB::commit();

            return redirect()->route('pembayaran-pranota-stock.index')
                           ->with('success', 'Pembayaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pembayaran Pranota Stock: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = PembayaranPranotaStock::with(['pranotaStocks.creator', 'createdBy'])->findOrFail($id);
        return view('pembayaran-pranota-stock.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = PembayaranPranotaStock::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Restore pranota status
            foreach($item->pranotaStocks as $pranota) {
                $pranota->update(['status' => 'approved']);
            }

            // Delete accounting entries
            $this->coaTransactionService->deleteTransactionByReference($item->nomor_pembayaran);

            $item->delete();
            DB::commit();
            return redirect()->route('pembayaran-pranota-stock.index')->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
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
            'nomor_pembayaran' => $this->generateNomorPembayaranSIS()
        ]);
    }
}
