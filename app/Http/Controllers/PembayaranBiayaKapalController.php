<?php

namespace App\Http\Controllers;

use App\Models\PembayaranBiayaKapal;
use App\Models\BiayaKapal;
use App\Models\Coa;
use App\Models\NomorTerakhir;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PembayaranBiayaKapalController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
        $this->middleware('auth');
        $this->middleware('can:pembayaran-biaya-kapal-view')->only(['index', 'show']);
        $this->middleware('can:pembayaran-biaya-kapal-create')->only(['create', 'store']);
        $this->middleware('can:pembayaran-biaya-kapal-edit')->only(['edit', 'update']);
        $this->middleware('can:pembayaran-biaya-kapal-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BiayaKapal::with(['klasifikasiBiaya', 'pembayarans']);

        // Filter by status pembayaran
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        // Search by invoice number, vessel name or vendor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_invoice', 'like', "%{$search}%")
                  ->orWhere('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('nama_vendor', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        $biayaKapalList = $query->orderBy('tanggal', 'desc')->paginate(15);

        $statuses = [
            'pending' => 'Belum Lunas',
            'paid' => 'Lunas',
            'cancelled' => 'Dibatalkan'
        ];

        return view('pembayaran-biaya-kapal.index', compact('biayaKapalList', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $biayaKapalQuery = BiayaKapal::query()->pending();

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $biayaKapalQuery->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // If biaya_kapal_id is provided, filter for specific invoice
        if ($request->filled('biaya_kapal_id')) {
            $biayaKapalQuery->where('id', $request->biaya_kapal_id);
        }

        $biayaKapals = $biayaKapalQuery
            ->with(['klasifikasiBiaya'])
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        // Get akun COA for bank selection
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        $nomorPembayaran = $this->generateNomorPembayaran();

        return view('pembayaran-biaya-kapal.create', compact('biayaKapals', 'nomorPembayaran', 'akunCoa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'biaya_kapal_ids' => ['required', 'array', 'min:1'],
            'biaya_kapal_ids.*' => ['exists:biaya_kapals,id'],
            'tanggal_pembayaran' => 'required|date',
            'jenis_transaksi' => ['required', Rule::in(['debit', 'kredit'])],
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'nomor_accurate' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Get or create PBK modul
            $modulNomor = NomorTerakhir::firstOrCreate(
                ['modul' => 'PBK'],
                ['nomor_terakhir' => 0, 'keterangan' => 'pembayaran biaya kapal']
            );
            
            // Generate number
            $nomorPembayaran = $this->generateNomorPembayaran();
            $modulNomor->increment('nomor_terakhir');

            $pembayaran = PembayaranBiayaKapal::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'bank' => $request->bank,
                'jenis_transaksi' => $request->jenis_transaksi,
                'total_pembayaran' => $request->total_pembayaran,
                'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian ?? 0,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status_pembayaran' => 'paid',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($validated['biaya_kapal_ids'] as $id) {
                $biayaKapal = BiayaKapal::findOrFail($id);
                
                // Use total_biaya if available, else use nominal
                $subtotal = $biayaKapal->total_biaya ?? $biayaKapal->nominal;

                $pembayaran->biayaKapals()->attach($id, [
                    'nominal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $biayaKapal->update([
                    'status_pembayaran' => 'paid',
                ]);
            }

            // Accounting integration if service supports it
            if (method_exists($this->coaTransactionService, 'pembayaranBiayaKapal')) {
                $this->coaTransactionService->pembayaranBiayaKapal($pembayaran);
            }

            DB::commit();

            return redirect()->route('pembayaran-biaya-kapal.index')
                ->with('success', 'Pembayaran biaya kapal berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing pembayaran biaya kapal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pembayaran = PembayaranBiayaKapal::with(['biayaKapals.klasifikasiBiaya', 'creator'])->findOrFail($id);
        return view('pembayaran-biaya-kapal.show', compact('pembayaran'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pembayaran = PembayaranBiayaKapal::findOrFail($id);

        DB::beginTransaction();
        try {
            // Restore status of associated biaya kapals
            foreach ($pembayaran->biayaKapals as $biayaKapal) {
                $biayaKapal->update([
                    'status_pembayaran' => 'pending',
                ]);
            }

            // Remove items
            $pembayaran->biayaKapals()->detach();
            
            // Delete payment record
            $pembayaran->delete();

            DB::commit();
            return redirect()->route('pembayaran-biaya-kapal.index')
                ->with('success', 'Pembayaran berhasil dibatalkan dan dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pembayaran: ' . $e->getMessage());
        }
    }

    private function generateNomorPembayaran()
    {
        $modul = NomorTerakhir::where('modul', 'PBK')->first();
        $nextNumber = $modul ? $modul->nomor_terakhir + 1 : 1;
        return 'PBK-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
